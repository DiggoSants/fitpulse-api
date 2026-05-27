<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Plan;
use App\Models\Student;
use App\Models\Instructor;
use App\Models\Enrollment;
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

        $plans = Plan::active()->get();

        return view('enrollments.index', compact('plans'));
    }

    public function store(Request $request, BillingService $billingService)
    {
        $request->validate([
            'plan_id'        => ['required', 'exists:plans,id'],
            'invite_code'    => ['required', 'string'],
            'payment_method' => ['required', 'in:credit_card,pix,boleto'],
        ], [
            'payment_method.required' => 'Informe o metodo de pagamento',
            'payment_method.in' => 'Metodo de pagamento invalido.',
            'plan_id.required'     => 'Selecione um plano',
            'plan_id.exists'       => 'Plano inválido',
            'invite_code.required' => 'Insira o código do seu instrutor',
        ]);

        $instructor = Instructor::where('invite_code', strtoupper($request->invite_code))->first();

        if (!$instructor) {
            return back()
                ->withInput()
                ->withErrors([
                    'invite_code' => 'Código de instrutor inválido.',
                ]);
        }

        /** @var \App\Models\User $user */
        $user    = Auth::user();
        $student = Student::where('user_id', $user->id)->firstOrFail();

        // Cancela matrículas ativas anteriores
        $plan      = Plan::where('status', 'active')->findOrFail($request->plan_id);
        $startDate = Carbon::today();
        $endDate   = $startDate->copy()->addDays($plan->duration_days);

        $billing = DB::transaction(function () use ($student, $instructor, $plan, $startDate, $endDate, $request, $billingService) {
            $lockedStudent = Student::whereKey($student->id)->lockForUpdate()->firstOrFail();

            if ($lockedStudent->isEnrolled()) {
                throw ValidationException::withMessages([
                    'plan_id' => 'Voce ja possui uma matricula ativa.',
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
                'instructor_id' => $instructor->id,
            ]);

            return $billingService->createForEnrollment($lockedStudent, $enrollment, $request->payment_method);
        });

        return redirect()
            ->route('dashboard')
            ->with('success', 'Matricula realizada! ' . $billingService->messageForStatus($billing->status));
    }

    public function cancel(Request $request, $id)
    {
        /** @var \App\Models\User $user */
        $user       = Auth::user();
        $enrollment = Enrollment::with('student')->findOrFail($id);

        // Aluno só pode cancelar a própria matrícula
        if ($user->isStudent()) {

            $student = Student::where('user_id', $user->id)->first();

            if (
                !$student ||
                $enrollment->student_id !== $student->id
            ) {
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

        // Cancela matrícula
        $enrollment->update([
            'status'       => 'cancelled',
            'cancelled_at' => now(),
        ]);

        // Atualiza status do aluno
        $student = $enrollment->student;

        if (!$student->isEnrolled()) {
            $student->update([
                'instructor_id' => null,
                'status'        => 'blocked',
                'is_defaulter'  => false,
            ]);
        }

        if (!$request->expectsJson()) {
            return redirect('/')->with('success', 'Plano cancelado com sucesso.');
        }

        return response()->json([
            'message' => 'Matrícula cancelada com sucesso.',
            'data'    => [
                'enrollment_id' => $enrollment->id,
                'cancelled_at'  => $enrollment->cancelled_at->format('d/m/Y H:i'),
            ],
        ]);
    }
}
