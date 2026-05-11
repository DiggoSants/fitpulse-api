<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Plan;
use App\Models\Enrollment;
use App\Models\Student;

class ReportController extends Controller
{
    public function plansComparative(Request $request)
    {
        $plans = Plan::active()
            ->withCount([
                'enrollments as active_students_count' => function ($query) {
                    $query->select(DB::raw('COUNT(DISTINCT student_id)'))
                          ->where('status', 'active')
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

        if ($request->expectsJson()) {
            return response()->json(['data' => $plans]);
        }

        return view('reports.plans-comparative', compact('plans'));
    }

    public function plansCancellations(Request $request)
    {
        $request->validate([
            'start_date' => ['nullable', 'date_format:Y-m-d'],
            'end_date'   => ['nullable', 'date_format:Y-m-d', 'after_or_equal:start_date'],
        ], [
            'start_date.date_format' => 'Data inicial inválida. Use o formato AAAA-MM-DD.',
            'end_date.date_format'   => 'Data final inválida. Use o formato AAAA-MM-DD.',
            'end_date.after_or_equal'=> 'A data final deve ser igual ou posterior à data inicial.',
        ]);

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

        if ($request->expectsJson()) {
            return response()->json([
                'data'    => $cancellations,
                'filters' => [
                    'start_date' => $request->start_date,
                    'end_date'   => $request->end_date,
                ],
            ]);
        }

        return view('reports.plans-cancellations', compact('cancellations'));
    }

    public function plansLoyalty(Request $request)
    {
        $enrollments = Enrollment::with(['student.user', 'plan'])
            ->where('status', 'active')
            ->where('end_date', '>=', now()->toDateString())
            ->orderBy('start_date', 'asc')
            ->get()
            ->groupBy('student_id')
            ->map(function ($group) {
                $oldest  = $group->first();
                $current = $group->last();

                $daysActive = (int) $oldest->start_date->startOfDay()
                    ->diffInDays(now()->startOfDay());

                return [
                    'student_name'  => $oldest->student->user->name,
                    'student_email' => $oldest->student->user->email,
                    'plan_name'     => $current->plan->name,
                    'start_date'    => $oldest->start_date->format('d/m/Y'),
                    'end_date'      => $current->end_date->format('d/m/Y'),
                    'days_active'   => $daysActive,
                ];
            })
            ->sortByDesc('days_active')
            ->values();

        if ($request->expectsJson()) {
            return response()->json(['data' => $enrollments]);
        }

        return view('reports.plans-loyalty', compact('enrollments'));
    }

    public function usersDelinquency(Request $request)
    {
        $delinquents = Student::with(['user'])
            ->where(function ($q) {
                $q->where('status', 'delinquent')
                  ->orWhere('is_defaulter', true);
            })
            ->get()
            ->map(function ($student) {
                return [
                    'id'             => $student->id,
                    'name'           => $student->user->name,
                    'email'          => $student->user->email,
                    'status'         => $student->status,
                    'payment_status' => $student->paymentStatus(),
                ];
            });

        $cancelled = Enrollment::with(['student.user', 'plan'])
            ->where('status', 'cancelled')
            ->whereNotNull('cancelled_at')
            ->latest('cancelled_at')
            ->get()
            ->map(function ($enrollment) {
                return [
                    'id'           => $enrollment->student->id,
                    'name'         => $enrollment->student->user->name,
                    'email'        => $enrollment->student->user->email,
                    'plan_name'    => $enrollment->plan->name,
                    'cancelled_at' => $enrollment->cancelled_at->format('d/m/Y H:i'),
                ];
            });

        $inactiveThreshold = now()->subDays(30);

        $inactive = Student::with(['user', 'frequencies'])
            ->where('status', 'active')
            ->whereHas('enrollments', function ($q) {
                $q->where('status', 'active')
                  ->where('end_date', '>=', now()->toDateString());
            })
            ->get()
            ->filter(function ($student) use ($inactiveThreshold) {
                $lastFreq = $student->frequencies->sortByDesc('created_at')->first();
                return !$lastFreq || $lastFreq->created_at->lt($inactiveThreshold);
            })
            ->map(function ($student) {
                $lastFreq = $student->frequencies->sortByDesc('created_at')->first();
                return [
                    'id'             => $student->id,
                    'name'           => $student->user->name,
                    'email'          => $student->user->email,
                    'last_frequency' => $lastFreq
                        ? $lastFreq->created_at->format('d/m/Y H:i')
                        : 'Nunca registrou presença',
                    'days_inactive'  => $lastFreq
                        ? (int) $lastFreq->created_at->diffInDays(now())
                        : null,
                ];
            })
            ->values();

        if ($request->expectsJson()) {
            return response()->json([
                'data' => [
                    'delinquents' => $delinquents,
                    'cancelled'   => $cancelled,
                    'inactive'    => $inactive,
                ],
                'summary' => [
                    'total_delinquents' => $delinquents->count(),
                    'total_cancelled'   => $cancelled->count(),
                    'total_inactive'    => $inactive->count(),
                ],
            ]);
        }

        return view('reports.users-delinquency', compact('delinquents', 'cancelled', 'inactive'));
    }

    public function plansOccupation(Request $request)
    {
        $occupation = Plan::withCount([
                'enrollments as active_students_count' => function ($query) {
                    $query->where('status', 'active')
                          ->where('end_date', '>=', now()->toDateString());
                }
            ])
            ->get()
            ->map(function ($plan) {
                return [
                    'plan_id'         => $plan->id,
                    'plan_name'       => $plan->name,
                    'plan_status'     => $plan->status,
                    'price'           => $plan->price,
                    'duration_days'   => $plan->duration_days,
                    'active_students' => $plan->active_students_count,
                ];
            })
            ->sortByDesc('active_students')
            ->values();

        $totalActive = $occupation->sum('active_students');

        $occupation = $occupation->map(function ($item) use ($totalActive) {
            $item['percentage'] = $totalActive > 0
                ? round(($item['active_students'] / $totalActive) * 100, 1)
                : 0;
            return $item;
        });

        if ($request->expectsJson()) {
            return response()->json([
                'data'    => $occupation,
                'summary' => [
                    'total_active_students' => $totalActive,
                    'total_plans'           => $occupation->count(),
                ],
            ]);
        }

        return view('reports.plans-occupation', compact('occupation', 'totalActive'));
    }
}
