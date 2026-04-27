<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Student;
use App\Models\Plan;
use App\Models\Enrollment;
use App\Models\PlanRenewal;
use App\Models\Billing;
use Carbon\Carbon;

class RenewalController extends Controller
{
    public function renew(Request $request)
    {
        $request->validate([
            'plan_id' => ['required', 'exists:plans,id'],
        ], [
            'plan_id.required' => 'Selecione um plano para renovar',
            'plan_id.exists'   => 'Plano inválido',
        ]);

        /** @var \App\Models\User $user */
        $user    = Auth::user();
        $student = Student::where('user_id', $user->id)->firstOrFail();

        $plan = Plan::where('id', $request->plan_id)
            ->where('status', 'active')
            ->firstOrFail();

        $currentEnrollment = $student->enrollments()
            ->where('status', 'active')
            ->latest('end_date')
            ->first();

        if (!$currentEnrollment) {
            return response()->json([
                'message' => 'Nenhuma matrícula encontrada para renovar. Use o fluxo de matrícula.',
            ], 422);
        }

        DB::transaction(function () use ($student, $plan, $currentEnrollment) {
            // Nova matrícula começa no dia seguinte ao vencimento da atual
            $startDate = $currentEnrollment->end_date->addDay();
            $endDate   = $startDate->copy()->addDays($plan->duration_days);
            $newEnrollment = Enrollment::create([
                'student_id' => $student->id,
                'plan_id'    => $plan->id,
                'start_date' => $startDate,
                'end_date'   => $endDate,
                'status'     => 'active',
            ]);

            // Registra renovação para histórico
            PlanRenewal::create([
                'student_id'        => $student->id,
                'old_enrollment_id' => $currentEnrollment->id,
                'new_enrollment_id' => $newEnrollment->id,
                'plan_id'           => $plan->id,
                'renewed_at'        => now(),
            ]);

            // Cria billing pendente — aluno tem 1 dia para pagar
            Billing::create([
                'student_id'    => $student->id,
                'plan_id'       => $plan->id,
                'enrollment_id' => $newEnrollment->id,
                'amount'        => $plan->price,
                'status'        => 'pending',
                'paid_at'       => null,
            ]);

            // Marca renewed_at e mantém status active por 1 dia
            $student->update([
                'renewed_at'   => now(),
                'status'       => 'active',
                'is_defaulter' => false,
            ]);
        });

        return redirect()
            ->route('plans.renewals')
            ->with('success', 'Plano renovado com sucesso! Você tem 1 dia para realizar o pagamento.');
    }
    public function history()
    {
        /** @var \App\Models\User $user */
        $user    = Auth::user();
        $student = Student::where('user_id', $user->id)->firstOrFail();

        $activeEnrollment = $student->enrollments()
            ->where('status', 'active')
            ->where('end_date', '>=', now()->toDateString())
            ->latest('end_date')
            ->first();

        $plans = Plan::where('status', 'active')->get();

        $renewals = PlanRenewal::with(['plan', 'oldEnrollment', 'newEnrollment'])
            ->where('student_id', $student->id)
            ->orderBy('renewed_at', 'desc')
            ->get();

        return view('plans.renew', compact('activeEnrollment', 'plans', 'renewals'));
    }
}

