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
                    'id'                   => $plan->id,
                    'name'                 => $plan->name,
                    'description'          => $plan->description,
                    'price'                => $plan->price,
                    'duration_days'        => $plan->duration_days,
                    'benefits'             => $plan->benefits,
                    'active_students'      => $plan->active_students_count,
                ];
            });

        return response()->json([
            'data' => $plans,
        ]);
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
                    'student_name'   => $enrollment->student->user->name,
                    'student_email'  => $enrollment->student->user->email,
                    'plan_name'      => $enrollment->plan->name,
                    'start_date'     => $enrollment->start_date->format('d/m/Y'),
                    'end_date'       => $enrollment->end_date->format('d/m/Y'),
                    'cancelled_at'   => $enrollment->cancelled_at->format('d/m/Y H:i'),
                ];
            });

        return response()->json([
            'data'    => $cancellations,
            'filters' => [
                'start_date' => $request->start_date,
                'end_date'   => $request->end_date,
            ],
        ]);
    }

    public function plansLoyalty()
    {
        $enrollments = Enrollment::with(['student.user', 'plan'])
            ->where('status', 'active')
            ->where('end_date', '>=', now()->toDateString())
            ->get()
            ->map(function ($enrollment) {
                $daysActive = $enrollment->start_date->diffInDays(now());

                return [
                    'student_name'  => $enrollment->student->user->name,
                    'student_email' => $enrollment->student->user->email,
                    'plan_name'     => $enrollment->plan->name,
                    'start_date'    => $enrollment->start_date->format('d/m/Y'),
                    'end_date'      => $enrollment->end_date->format('d/m/Y'),
                    'days_active'   => $daysActive,
                ];
            })
            ->sortByDesc('days_active')
            ->values();

        return response()->json([
            'data' => $enrollments,
        ]);
    }
}