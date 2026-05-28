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
use App\Services\BillingService;
use Carbon\Carbon;

class RenewalController extends Controller
{
    public function renew(Request $request, BillingService $billingService)
    {
        $request->validate([
            'plan_id'        => ['required', 'exists:plans,id'],
            'payment_method' => ['required', 'in:credit_card,debit_card,pix,boleto'],
        ], [
            'plan_id.required'        => 'Selecione um plano para renovar',
            'plan_id.exists'          => 'Plano inválido',
            'payment_method.required' => 'Informe o metodo de pagamento',
            'payment_method.in'       => 'Metodo de pagamento invalido.',
        ]);

        /** @var \App\Models\User $user */
        $user    = Auth::user();
        $student = Student::where('user_id', $user->id)->firstOrFail();

        $plan = Plan::where('id', $request->plan_id)
            ->where('status', 'active')
            ->firstOrFail();

        // Verifica se há pagamentos pendentes não quitados
        $pendingBilling = Billing::where('student_id', $student->id)
            ->where('status', 'pending')
            ->first();

        if ($pendingBilling) {
            return back()
                ->with('error', 'Você possui pagamentos pendentes. Complete o pagamento antes de renovar o plano.');
        }

        $currentEnrollment = $student->enrollments()
            ->where('status', 'active')
            ->latest('end_date')
            ->first();

        if (!$currentEnrollment) {
            if (!$request->expectsJson()) {
                return redirect()
                    ->route('enrollment.index')
                    ->with('info', 'Nenhuma matrícula encontrada para renovar. Use o fluxo de matrícula.');
            }

            return response()->json([
                'message' => 'Nenhuma matrícula encontrada para renovar. Use o fluxo de matrícula.',
            ], 422);
        }

        $billing = DB::transaction(function () use ($student, $plan, $currentEnrollment, $request, $billingService) {
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

            // Marca renewed_at e mantém status active por 1 dia
            $student->update([
                'renewed_at'   => now(),
                'status'       => 'active',
                'is_defaulter' => false,
            ]);

            return $billingService->createForEnrollment($student, $newEnrollment, $request->payment_method);
        });

        return redirect()
            ->route('plans.renewals')
            ->with('success', 'Plano renovado! ' . $billingService->messageForStatus($billing->status));
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
