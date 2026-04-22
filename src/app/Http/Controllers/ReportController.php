<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Plan;
use App\Models\Enrollment;

class ReportController extends Controller
{
    public function plansComparative()
    {
        $plans = Plan::active()
            ->withCount([
                'enrollments as active_students_count' => function ($query) {
                    $query->where('status', 'active')
                        ->where('end_date', '>=', now()->toDateString());
                }
            ])
            ->get()
            ->map(function ($plan) {
                return [
                    'id'              => $plan->id,
                    'name'            => $plan->name,
                    'description'     => $plan->description,
                    'price'           => $plan->price,
                    'duration_days'   => $plan->duration_days,
                    'benefits'        => $plan->benefits,
                    'active_students' => $plan->active_students_count,
                ];
            });

        return view('reports.plans-comparative', compact('plans'));
    }

    public function plansCancellations(Request $request)
    {
        $query = Enrollment::with(['student.user', 'plan'])
            ->where('status', 'cancelled')
            ->whereNotNull('cancelled_at');

        if ($request->filled('start_date')) {
            $query->where('cancelled_at', '>=', $request->start_date . ' 00:00:00');
        }

        if ($request->filled('end_date')) {
            $query->where('cancelled_at', '<=', $request->end_date . ' 23:59:59');
        }

        $cancellations = $query->orderBy('cancelled_at', 'desc')
            ->get()
            ->map(function ($enrollment) {
                return [
                    'student_name'  => $enrollment->student->user->name,
                    'student_email' => $enrollment->student->user->email,
                    'plan_name'     => $enrollment->plan->name,
                    'start_date'    => $enrollment->start_date->format('d/m/Y'),
                    'end_date'      => $enrollment->end_date->format('d/m/Y'),
                    'cancelled_at'  => $enrollment->cancelled_at->format('d/m/Y H:i'),
                ];
            });

        return view('reports.plans-cancellations', compact('cancellations'));
    }

    public function plansLoyalty()
    {
        // Busca todas as matrículas ativas e agrupa por aluno,
        // mantendo apenas a matrícula mais antiga de cada um (start_date menor)
        // para calcular o tempo real de fidelidade.
        $enrollments = Enrollment::with(['student.user', 'plan'])
            ->where('status', 'active')
            ->where('end_date', '>=', now()->toDateString())
            ->orderBy('start_date', 'asc') // mais antigas primeiro para o groupBy manter a 1ª
            ->get()
            ->groupBy('student_id')        // agrupa por aluno — remove duplicatas de renovação
            ->map(function ($group) {
                // Pega a matrícula mais antiga do aluno
                $oldest = $group->first();
                // Pega o plano atual (matrícula mais recente)
                $current = $group->last();

                $daysActive = (int) now()->startOfDay()
                    ->diffInDays($oldest->start_date->startOfDay());

                return [
                    'student_name'  => $oldest->student->user->name,
                    'student_email' => $oldest->student->user->email,
                    'plan_name'     => $current->plan->name,   // plano atual
                    'start_date'    => $oldest->start_date->format('d/m/Y'), // desde quando é aluno
                    'end_date'      => $current->end_date->format('d/m/Y'),  // vencimento atual
                    'days_active'   => $daysActive,
                ];
            })
            ->sortByDesc('days_active')
            ->values();

        $avgDays     = $enrollments->avg('days_active') ?? 0;
        $maxDays     = $enrollments->max('days_active') ?? 0;
        $totalActive = $enrollments->count();

        return view('reports.plans-loyalty', compact('enrollments', 'avgDays', 'maxDays', 'totalActive'));
    }
}