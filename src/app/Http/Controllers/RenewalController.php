<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Student;
use App\Models\Plan;
use App\Models\Enrollment;
use App\Models\PlanRenewal;

class RenewalController extends Controller
{
    // Exibe a tela de renovação com plano atual e lista de planos disponíveis
    public function history()
    {
        /** @var \App\Models\User $user */
        $user    = Auth::user();
        $student = Student::where('user_id', $user->id)->firstOrFail();

        $activeEnrollment = $student->activeEnrollment();

        $plans = Plan::where('status', 'active')->orderBy('price')->get();

        $renewals = PlanRenewal::with(['plan', 'oldEnrollment', 'newEnrollment'])
            ->where('student_id', $student->id)
            ->orderBy('renewed_at', 'desc')
            ->get();

        return view('plans.renew', compact('activeEnrollment', 'plans', 'renewals'));
    }

    // Processa a renovação e redireciona com mensagem
    public function renew(Request $request)
    {
        $request->validate([
            'plan_id' => ['required', 'exists:plans,id'],
        ], [
            'plan_id.required' => 'Selecione um plano para renovar.',
            'plan_id.exists'   => 'Plano inválido.',
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
            return back()->with('error', 'Nenhuma matrícula encontrada. Use o fluxo de matrícula.');
        }

        DB::transaction(function () use ($student, $plan, $currentEnrollment) {
            $startDate = $currentEnrollment->end_date->copy()->addDay();
            $endDate   = $startDate->copy()->addDays($plan->duration_days);

            $newEnrollment = Enrollment::create([
                'student_id' => $student->id,
                'plan_id'    => $plan->id,
                'start_date' => $startDate,
                'end_date'   => $endDate,
                'status'     => 'active',
            ]);

            PlanRenewal::create([
                'student_id'        => $student->id,
                'old_enrollment_id' => $currentEnrollment->id,
                'new_enrollment_id' => $newEnrollment->id,
                'plan_id'           => $plan->id,
                'renewed_at'        => now(),
            ]);
        });

        return redirect()->route('plans.renewals')->with('success', 'Plano renovado com sucesso!');
    }
}