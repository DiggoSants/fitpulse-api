<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Student;
use App\Models\Instructor;
use App\Models\Plan;
use App\Models\Enrollment;
use App\Models\Receptionist;
use App\Services\BillingService;
use Carbon\Carbon;

class ReceptionController extends Controller
{
    public function pendingEnrollment()
    {
        return redirect()->route('dashboard');
    }

    public function pendingEnrollmentData()
    {
        User::whereDoesntHave('student')
            ->whereDoesntHave('instructor')
            ->whereDoesntHave('manager')
            ->whereDoesntHave('receptionist')
            ->get(['id'])
            ->each(function (User $user) {
                Student::firstOrCreate(
                    ['user_id' => $user->id],
                    ['status' => 'active']
                );
            });

        $students = Student::with('user')
            ->whereHas('user', function ($q) {
                $q->whereDoesntHave('instructor')
                  ->whereDoesntHave('manager')
                  ->whereDoesntHave('receptionist');
            })
            ->whereDoesntHave('enrollments', function ($q) {
                $q->where('status', 'active')
                  ->where('end_date', '>=', now()->toDateString());
            })
            ->get()
            ->map(function ($student) {
                return [
                    'id'     => $student->id,
                    'name'   => $student->user->name,
                    'email'  => $student->user->email,
                    'status' => $student->status,
                ];
            })
            ->values();

        return response()->json([
            'data'  => $students,
            'total' => $students->count(),
        ]);
    }

    public function activePlans()
    {
        $plans = Plan::active()
            ->where(function ($query) {
                $query->where('is_trial', false)
                    ->orWhereNull('is_trial');
            })
            ->orderedByPrice()
            ->get(['id', 'name', 'price', 'duration_days']);

        $startDate = Carbon::today();
        $trialOptions = Plan::active()
            ->trials()
            ->whereNotNull('trial_days')
            ->where('trial_days', '>', 0)
            ->orderBy('trial_days')
            ->orderBy('name')
            ->get(['id', 'name', 'trial_days'])
            ->map(fn (Plan $plan) => $this->trialOptionPayload($plan, $startDate))
            ->values();

        return response()->json([
            'data' => $plans,
            'trial_available' => $trialOptions->isNotEmpty(),
            'trial_options' => $trialOptions,
        ]);
    }

    public function availableInstructors()
    {
        $instructors = Instructor::with('user')
            ->get()
            ->map(function ($instructor) {
                return [
                    'id'          => $instructor->id,
                    'name'        => $instructor->user->name,
                    'specialty'   => $instructor->specialty ?? '—',
                    'invite_code' => $instructor->invite_code,
                    'students'    => $instructor->students()->count(),
                ];
            });

        return response()->json(['data' => $instructors]);
    }

    public function enroll(Request $request, BillingService $billingService)
    {
        $request->validate([
            'student_id'     => ['required', 'exists:students,id'],
            'plan_id'        => ['required', 'exists:plans,id'],
            'instructor_id'  => ['required', 'exists:instructors,id'],
            'payment_method' => ['required', 'in:credit_card,debit_card,pix'],
        ], [
            'student_id.required'    => 'Selecione o aluno',
            'plan_id.required'       => 'Selecione o plano',
            'instructor_id.required' => 'Selecione o instrutor',
            'payment_method.required' => 'Informe o metodo de pagamento',
            'payment_method.in' => 'Metodo de pagamento invalido.',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $student    = Student::findOrFail($request->student_id);
        $plan       = Plan::where('id', $request->plan_id)->where('status', 'active')->firstOrFail();
        $instructor = Instructor::findOrFail($request->instructor_id);

        if ($plan->isTrial()) {
            return response()->json([
                'message' => 'Use a opcao de teste gratis para aplicar esta duracao.',
            ], 422);
        }

        if (!$student->user || !$student->user->isStudent()) {
            return response()->json([
                'message' => 'Apenas alunos podem ser matriculados.',
            ], 422);
        }

        if ($student->isEnrolled()) {
            return response()->json([
                'message' => 'Este aluno já possui uma matrícula ativa.',
            ], 422);
        }

        $receptionist = $user->isReceptionist()
            ? Receptionist::where('user_id', $user->id)->first()
            : null;

        $startDate = Carbon::today();
        $endDate   = $startDate->copy()->addDays($plan->duration_days);

        [$enrollment, $billing] = DB::transaction(function () use ($student, $plan, $receptionist, $startDate, $endDate, $instructor, $request, $billingService) {
            $lockedStudent = Student::whereKey($student->id)->lockForUpdate()->firstOrFail();

            if ($lockedStudent->isEnrolled()) {
                abort(response()->json([
                    'message' => 'Este aluno ja possui uma matricula ativa.',
                ], 422));
            }

            $enrollment = Enrollment::create([
                'student_id'      => $lockedStudent->id,
                'plan_id'         => $plan->id,
                'receptionist_id' => $receptionist?->id,
                'start_date'      => $startDate,
                'end_date'        => $endDate,
                'status'          => 'active',
            ]);

            $lockedStudent->update(['instructor_id' => $instructor->id]);

            $billing = $billingService->createForEnrollment($lockedStudent, $enrollment, $request->payment_method);

            return [$enrollment, $billing];
        });

        return response()->json([
            'message' => 'Matricula realizada com sucesso! ' . $billingService->messageForStatus($billing->status),
            'data'    => [
                'enrollment_id'  => $enrollment->id,
                'billing_id'     => $billing->id,
                'student'        => $student->user->name,
                'plan'           => $plan->name,
                'instructor'     => $instructor->user->name,
                'payment_method' => $billing->payment_method,
                'payment_status' => $billing->status,
                'start_date'     => $enrollment->start_date->format('d/m/Y'),
                'end_date'       => $enrollment->end_date->format('d/m/Y'),
                'receptionist'   => $receptionist?->user->name ?? 'Gerente',
            ],
        ], 201);
    }

    public function enrollTrial(Request $request)
    {
        $request->validate([
            'student_id'     => ['required', 'exists:students,id'],
            'trial_plan_id' => ['required', 'exists:plans,id'],
        ], [
            'student_id.required' => 'Selecione o aluno.',
            'trial_plan_id.required' => 'Selecione uma duracao de teste gratis.',
            'trial_plan_id.exists' => 'Duracao de teste gratis invalida.',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $student = Student::with('user')->findOrFail($request->student_id);
        $trialPlan = $this->availableTrialPlan($request->integer('trial_plan_id'));

        if (!$trialPlan) {
            return response()->json([
                'message' => 'Esta duracao de teste gratis nao esta disponivel.',
            ], 422);
        }

        if (!$student->user || !$student->user->isStudent()) {
            return response()->json([
                'message' => 'Apenas alunos podem receber teste gratis.',
            ], 422);
        }

        if ($student->isEnrolled()) {
            return response()->json([
                'message' => 'Este aluno ja possui uma matricula ativa.',
            ], 422);
        }

        if ($student->hasUsedTrial()) {
            return response()->json([
                'message' => 'Este aluno ja utilizou o teste gratis.',
            ], 422);
        }

        $receptionist = $user->isReceptionist()
            ? Receptionist::where('user_id', $user->id)->first()
            : null;

        $startDate = Carbon::today();
        $endDate = $startDate->copy()->addDays((int) $trialPlan->trial_days);

        $enrollment = DB::transaction(function () use ($student, $trialPlan, $receptionist, $startDate, $endDate) {
            $lockedStudent = Student::with('user')->whereKey($student->id)->lockForUpdate()->firstOrFail();

            if ($lockedStudent->isEnrolled()) {
                abort(response()->json([
                    'message' => 'Este aluno ja possui uma matricula ativa.',
                ], 422));
            }

            if ($lockedStudent->hasUsedTrial()) {
                abort(response()->json([
                    'message' => 'Este aluno ja utilizou o teste gratis.',
                ], 422));
            }

            return Enrollment::create([
                'student_id'      => $lockedStudent->id,
                'plan_id'         => $trialPlan->id,
                'receptionist_id' => $receptionist?->id,
                'start_date'      => $startDate,
                'end_date'        => $endDate,
                'status'          => 'active',
            ]);
        });

        return response()->json([
            'message' => "Teste gratis ativado por {$trialPlan->trial_days} dias.",
            'data'    => [
                'trial'          => true,
                'enrollment_id'  => $enrollment->id,
                'student'        => $student->user->name,
                'plan'           => $trialPlan->name,
                'trial_days'     => (int) $trialPlan->trial_days,
                'start_date'     => $enrollment->start_date->format('d/m/Y'),
                'end_date'       => $enrollment->end_date->format('d/m/Y'),
                'receptionist'   => $receptionist?->user->name ?? 'Gerente',
            ],
        ], 201);
    }

    private function availableTrialPlan(int $planId): ?Plan
    {
        return Plan::active()
            ->trials()
            ->whereNotNull('trial_days')
            ->where('trial_days', '>', 0)
            ->whereKey($planId)
            ->first();
    }

    private function trialOptionPayload(Plan $plan, Carbon $startDate): array
    {
        $endDate = $startDate->copy()->addDays((int) $plan->trial_days);

        return [
            'id' => $plan->id,
            'name' => $plan->name,
            'trial_days' => (int) $plan->trial_days,
            'start_date' => $startDate->toDateString(),
            'end_date' => $endDate->toDateString(),
            'start_date_formatted' => $startDate->format('d/m/Y'),
            'end_date_formatted' => $endDate->format('d/m/Y'),
        ];
    }
}
