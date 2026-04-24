<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Plan;
use App\Models\Enrollment;
use App\Models\Student;

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

        return response()->json(['data' => $plans]);
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
                return [
                    'student_name'  => $enrollment->student->user->name,
                    'student_email' => $enrollment->student->user->email,
                    'plan_name'     => $enrollment->plan->name,
                    'start_date'    => $enrollment->start_date->format('d/m/Y'),
                    'end_date'      => $enrollment->end_date->format('d/m/Y'),
                    'days_active'   => $enrollment->start_date->diffInDays(now()),
                ];
            })
            ->sortByDesc('days_active')
            ->values();

        return response()->json(['data' => $enrollments]);
    }

    public function usersDelinquency()
    {
        // INADIMPLENTES 
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

        //  CANCELADOS 
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

        //  INATIVOS (30 dias sem frequência) 
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
}
