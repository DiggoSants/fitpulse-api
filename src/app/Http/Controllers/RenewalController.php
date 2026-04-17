<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Student;
use App\Models\Plan;
use App\Models\Enrollment;
use App\Models\PlanRenewal;
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
        $plan    = Plan::where('id', $request->plan_id)
            ->where('status', 'active')
            ->firstOrFail();

        // Busca matrícula atual 
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

            // Cria a nova enrollment
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

        return response()->json([
            'message' => 'Plano renovado com sucesso!',
        ], 201);
    }

    public function history()
    {
        /** @var \App\Models\User $user */
        $user    = Auth::user();
        $student = Student::where('user_id', $user->id)->firstOrFail();

        $renewals = PlanRenewal::with(['plan', 'oldEnrollment', 'newEnrollment'])
            ->where('student_id', $student->id)
            ->orderBy('renewed_at', 'desc')
            ->get()
            ->map(function ($renewal) {
                return [
                    'plan_name'        => $renewal->plan->name,
                    'renewed_at'       => $renewal->renewed_at->format('d/m/Y H:i'),
                    'old_period'       => [
                        'start' => $renewal->oldEnrollment->start_date->format('d/m/Y'),
                        'end'   => $renewal->oldEnrollment->end_date->format('d/m/Y'),
                    ],
                    'new_period'       => [
                        'start' => $renewal->newEnrollment->start_date->format('d/m/Y'),
                        'end'   => $renewal->newEnrollment->end_date->format('d/m/Y'),
                    ],
                ];
            });

        return response()->json(['data' => $renewals]);
    }
}
