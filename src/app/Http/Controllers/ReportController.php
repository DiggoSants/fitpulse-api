<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Plan;
use App\Models\Enrollment;
use App\Models\Student;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function plansComparative()
    {
        $today = now()->toDateString();

        // IDs de students que são na verdade instrutores (excluir da contagem)
        $instructorStudentIds = DB::table('students')
            ->join('instructors', 'instructors.user_id', '=', 'students.user_id')
            ->pluck('students.id');

        // Pega apenas a matrícula mais recente de cada aluno real
        $latestEnrollments = DB::table('enrollments')
            ->select('plan_id', DB::raw('COUNT(DISTINCT enrollments.student_id) as active_students'))
            ->whereIn('enrollments.id', function ($sub) use ($today, $instructorStudentIds) {
                $sub->select(DB::raw('MAX(id)'))
                    ->from('enrollments')
                    ->where('status', 'active')
                    ->where('end_date', '>=', $today)
                    ->whereNotIn('student_id', $instructorStudentIds)
                    ->groupBy('student_id');
            })
            ->groupBy('plan_id')
            ->get()
            ->keyBy('plan_id');

        $plans = Plan::active()
            ->get()
            ->map(function ($plan) use ($latestEnrollments) {
                return [
                    'id'              => $plan->id,
                    'name'            => $plan->name,
                    'description'     => $plan->description,
                    'price'           => $plan->price,
                    'duration_days'   => $plan->duration_days,
                    'benefits'        => $plan->benefits,
                    'active_students' => $latestEnrollments->get($plan->id)?->active_students ?? 0,
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
        // Agrupa por aluno e calcula dias desde a primeira matrícula
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

        return view('reports.plans-loyalty', compact('enrollments'));
    }

    public function usersDelinquency()
    {
        // ── INADIMPLENTES ─────────────────────────────────────────────────────
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

        // ── CANCELADOS ────────────────────────────────────────────────────────
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

        // ── INATIVOS (30 dias sem frequência) ─────────────────────────────────
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

        return view('reports.users-delinquency', compact('delinquents', 'cancelled', 'inactive'));
    }

    public function plansOccupation()
    {
        $today = now()->toDateString();

        // IDs de students que são na verdade instrutores (excluir da contagem)
        $instructorStudentIds = DB::table('students')
            ->join('instructors', 'instructors.user_id', '=', 'students.user_id')
            ->pluck('students.id');

        // Pega apenas a matrícula mais recente de cada aluno real
        $latestEnrollments = DB::table('enrollments')
            ->select('plan_id', DB::raw('COUNT(DISTINCT enrollments.student_id) as active_students'))
            ->whereIn('enrollments.id', function ($sub) use ($today, $instructorStudentIds) {
                $sub->select(DB::raw('MAX(id)'))
                    ->from('enrollments')
                    ->where('status', 'active')
                    ->where('end_date', '>=', $today)
                    ->whereNotIn('student_id', $instructorStudentIds)
                    ->groupBy('student_id');
            })
            ->groupBy('plan_id')
            ->get()
            ->keyBy('plan_id');

        $totalActive = $latestEnrollments->sum('active_students');

        $occupation = Plan::all()
            ->map(function ($plan) use ($latestEnrollments, $totalActive) {
                $activeStudents = $latestEnrollments->get($plan->id)?->active_students ?? 0;
                return [
                    'plan_id'         => $plan->id,
                    'plan_name'       => $plan->name,
                    'plan_status'     => $plan->status,
                    'price'           => $plan->price,
                    'duration_days'   => $plan->duration_days,
                    'active_students' => $activeStudents,
                    'percentage'      => $totalActive > 0
                        ? round(($activeStudents / $totalActive) * 100, 1)
                        : 0,
                ];
            })
            ->sortByDesc('active_students')
            ->values();

        return view('reports.plans-occupation', compact('occupation', 'totalActive'));
    }
}