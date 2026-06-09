<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Plan;
use App\Models\Student;
use App\Models\Instructor;
use App\Models\Enrollment;
use App\Models\StudentSchedule;
use App\Models\InstructorAvailability;
use App\Services\BillingService;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class EnrollmentController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user    = Auth::user();
        $student = Student::where('user_id', $user->id)->first();

        if ($student && $student->isEnrolled()) {
            return redirect()->route('dashboard');
        }

        $plans = Plan::active()->orderedByPrice()->get();

        return view('enrollments.index', compact('plans'));
    }

    /**
     * Matrícula com distribuição automática de instrutor disponível.
     */
    public function store(Request $request, BillingService $billingService)
    {
        $request->validate([
            'plan_id'         => ['required', 'exists:plans,id'],
            'payment_method'  => ['required', 'in:credit_card,debit_card,pix'],
            'preferred_shift' => ['nullable', 'string', 'in:morning,afternoon,evening,full_day'],
        ], [
            'payment_method.required' => 'Informe o método de pagamento',
            'payment_method.in'       => 'Método de pagamento inválido.',
            'plan_id.required'        => 'Selecione um plano',
            'plan_id.exists'          => 'Plano inválido',
        ]);

        /** @var \App\Models\User $user */
        $user    = Auth::user();
        $student = Student::where('user_id', $user->id)->firstOrFail();

        // --------------------------------------------------------------
        // 1. Validar agenda do aluno (dias de treino)
        // --------------------------------------------------------------
        $studentScheduleDays = StudentSchedule::where('user_id', $user->id)
            ->where('active', true)
            ->pluck('week_day')
            ->toArray();

        if (empty($studentScheduleDays)) {
            return back()
                ->withInput()
                ->withErrors(['schedule' => 'Você precisa definir sua agenda de treino (dias da semana) antes de se matricular.']);
        }

        // --------------------------------------------------------------
        // 2. Turno preferido (padrão: full_day)
        // --------------------------------------------------------------
        $shift = $request->input('preferred_shift', 'full_day');

        // --------------------------------------------------------------
        // 3. Buscar instrutores que cobrem TODOS os dias da agenda no turno
        // --------------------------------------------------------------
        $availableInstructors = Instructor::whereHas('availability', function ($q) use ($studentScheduleDays, $shift) {
            $q->whereIn('week_day', $studentScheduleDays)
              ->where('shift', $shift)
              ->where('active', true);
        })
        ->with('user')
        ->get()
        ->filter(function ($instructor) use ($studentScheduleDays, $shift) {
            $availableDays = InstructorAvailability::where('instructor_id', $instructor->id)
                ->whereIn('week_day', $studentScheduleDays)
                ->where('shift', $shift)
                ->where('active', true)
                ->pluck('week_day')
                ->unique()
                ->toArray();
            return count(array_intersect($studentScheduleDays, $availableDays)) === count($studentScheduleDays);
        });

        if ($availableInstructors->isEmpty()) {
            $message = 'Nenhum instrutor disponível para os dias e horários da sua agenda. Entre em contato com a recepção.';
            if ($request->expectsJson()) {
                return response()->json(['message' => $message], 422);
            }
            return back()->withInput()->withErrors(['instructor' => $message]);
        }

        // --------------------------------------------------------------
        // 4. Selecionar instrutor com menos alunos (balanceamento de carga)
        // --------------------------------------------------------------
        $selectedInstructor = $availableInstructors->sortBy(function ($instructor) {
            return $instructor->students()->count();
        })->first();

        // --------------------------------------------------------------
        // 5. Processar plano e criar matrícula
        // --------------------------------------------------------------
        $plan      = Plan::where('status', 'active')->findOrFail($request->plan_id);
        $startDate = Carbon::today();
        $endDate   = $startDate->copy()->addDays($plan->duration_days);

        $billing = DB::transaction(function () use ($student, $selectedInstructor, $plan, $startDate, $endDate, $request, $billingService) {
            $lockedStudent = Student::whereKey($student->id)->lockForUpdate()->firstOrFail();

            if ($lockedStudent->isEnrolled()) {
                throw ValidationException::withMessages([
                    'plan_id' => 'Você já possui uma matrícula ativa.',
                ]);
            }

            $enrollment = Enrollment::create([
                'student_id' => $lockedStudent->id,
                'plan_id'    => $plan->id,
                'start_date' => $startDate,
                'end_date'   => $endDate,
                'status'     => 'active',
            ]);

            $lockedStudent->update([
                'instructor_id' => $selectedInstructor->id,
            ]);

            return $billingService->createForEnrollment($lockedStudent, $enrollment, $request->payment_method);
        });

        $instructorName = $selectedInstructor->user->name;
        $successMessage = "Matrícula realizada! Instrutor: {$instructorName}. " . $billingService->messageForStatus($billing->status);

        if ($request->expectsJson()) {
            return response()->json([
                'message'     => $successMessage,
                'instructor'  => $selectedInstructor->only(['id', 'user.name']),
                'enrollment'  => $enrollment ?? null,
            ]);
        }

        return redirect()
            ->route('dashboard')
            ->with('success', $successMessage);
    }

    /**
     * Cancela a matrícula respeitando o período já pago.
     */
    public function cancel(Request $request, $id)
    {
        /** @var \App\Models\User $user */
        $user       = Auth::user();
        $enrollment = Enrollment::with('student')->findOrFail($id);

        // Aluno só pode cancelar a própria matrícula
        if ($user->isStudent()) {
            $student = Student::where('user_id', $user->id)->first();

            if (!$student || $enrollment->student_id !== $student->id) {
                if (!$request->expectsJson()) {
                    return back()->with('error', 'Você não tem permissão para cancelar esta matrícula.');
                }

                return response()->json([
                    'message' => 'Você não tem permissão para cancelar esta matrícula.',
                ], 403);
            }
        } elseif (!$user->isManager()) {
            if (!$request->expectsJson()) {
                return back()->with('error', 'Apenas o próprio aluno ou um gerente podem cancelar matrículas.');
            }

            return response()->json([
                'message' => 'Apenas o próprio aluno ou um gerente podem cancelar matrículas.',
            ], 403);
        }

        // Verifica se já foi cancelada
        if ($enrollment->status === 'cancelled') {
            if (!$request->expectsJson()) {
                return redirect('/')->with('info', 'Esta matrícula já foi cancelada.');
            }

            return response()->json([
                'message' => 'Esta matrícula já foi cancelada.',
            ], 422);
        }

        // Verifica se a matrícula já expirou
        if ($enrollment->end_date->isPast()) {
            if (!$request->expectsJson()) {
                return back()->with('error', 'Matrícula já expirada. Não é possível cancelar.');
            }

            return response()->json([
                'message' => 'Matrícula já expirada.',
            ], 400);
        }

        // Cancela matrícula (mantém end_date inalterado)
        $enrollment->update([
            'status'       => 'cancelled',
            'cancelled_at' => now(),
        ]);

        $daysLeft = $enrollment->daysLeft();
        $endDate  = $enrollment->end_date->format('d/m/Y');

        $message = "Cancelamento solicitado. Você mantém acesso até {$endDate} (faltam {$daysLeft} dias).";

        if (!$request->expectsJson()) {
            return redirect('/')->with('success', $message);
        }

        return response()->json([
            'message'   => $message,
            'days_left' => $daysLeft,
            'end_date'  => $endDate,
        ]);
    }

    /**
     * Realiza matrícula de teste grátis (apenas uma vez por aluno)
     */
    public function trial(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->firstOrFail();

        // Verifica se já usou teste
        if ($student->hasUsedTrial()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Você já utilizou o teste grátis. Contrate um plano para continuar.'], 403);
            }
            return back()->with('error', 'Você já utilizou o teste grátis. Contrate um plano para continuar.');
        }

        // Busca o plano ativo que seja do tipo teste
        $trialPlan = Plan::where('is_trial', true)->where('status', 'active')->first();

        if (!$trialPlan) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Nenhum plano de teste disponível no momento.'], 404);
            }
            return back()->with('error', 'Nenhum plano de teste disponível.');
        }

        // Cria a matrícula com data fim baseada nos trial_days
        $startDate = now();
        $endDate   = $startDate->copy()->addDays($trialPlan->trial_days);

        $enrollment = Enrollment::create([
            'student_id' => $student->id,
            'plan_id'    => $trialPlan->id,
            'start_date' => $startDate,
            'end_date'   => $endDate,
            'status'     => 'active',
        ]);

        $message = "Teste grátis ativado! Você tem acesso até {$endDate->format('d/m/Y')}.";

        if ($request->expectsJson()) {
            return response()->json([
                'message'     => $message,
                'enrollment'  => $enrollment,
                'end_date'    => $endDate->toDateString(),
                'days_left'   => $enrollment->daysLeft(),
            ]);
        }

        return redirect()->route('dashboard')->with('success', $message);
    }
}