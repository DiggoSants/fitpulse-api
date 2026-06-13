<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Models\Plan;
use App\Models\Enrollment;
use App\Models\Student;
use App\Models\Billing;

class ReportController extends Controller
{
    public function plansComparative(Request $request)
    {
        // Formas de pagamento mais usadas por plano (billings confirmados)
        $paymentsByPlan = Billing::select('plan_id', 'payment_method', DB::raw('COUNT(*) as total'))
            ->where('status', 'confirmed')
            ->whereNotNull('payment_method')
            ->groupBy('plan_id', 'payment_method')
            ->orderBy('plan_id')
            ->orderByDesc('total')
            ->get()
            ->groupBy('plan_id')
            ->map(function ($methods) {
                // Pega os 3 métodos mais usados e formata os labels
                return $methods->take(3)->map(function ($m) {
                    return [
                        'method' => $m->payment_method,
                        'label'  => self::paymentLabel($m->payment_method),
                        'total'  => $m->total,
                    ];
                })->values();
            });

        $plans = Plan::active()
            ->withCount([
                'enrollments as active_students_count' => function ($query) {
                    $query->select(DB::raw('COUNT(DISTINCT student_id)'))
                          ->where('status', 'active')
                          ->where('end_date', '>=', now()->toDateString());
                }
            ])
            ->orderedByPrice()
            ->get()
            ->map(function ($plan) use ($paymentsByPlan) {
                $methods  = $paymentsByPlan->get($plan->id, collect());
                $revenue  = round($plan->price * $plan->active_students_count, 2);

                return [
                    'id'              => $plan->id,
                    'name'            => $plan->name,
                    'description'     => $plan->description,
                    'price'           => $plan->price,
                    'duration_days'   => $plan->duration_days,
                    'active_students' => $plan->active_students_count,
                    'revenue'         => $revenue,
                    'payment_methods' => $methods,  // [{method, label, total}]
                ];
            });

        if ($request->expectsJson()) {
            return response()->json(['data' => $plans]);
        }

        return view('reports.plans-comparative', compact('plans'));
    }

    /** Converte chave interna em label legível */
    private static function paymentLabel(string $method): string
    {
        return match (strtolower(trim($method))) {
            'pix'           => 'Pix',
            'credit_card',
            'credito',
            'credit'        => 'Crédito',
            'debit_card',
            'debito',
            'debit'         => 'Débito',
            'cash',
            'dinheiro'      => 'Dinheiro',
            'boleto'        => 'Boleto',
            'transferencia',
            'transfer'      => 'Transferência',
            default         => ucfirst($method),
        };
    }

    public function plansCancellations(Request $request)
    {
        $request->validate([
            'start_date' => ['nullable', 'date_format:Y-m-d'],
            'end_date'   => ['nullable', 'date_format:Y-m-d', 'after_or_equal:start_date'],
        ], [
            'start_date.date_format'  => 'Data inicial inválida. Use o formato AAAA-MM-DD.',
            'end_date.date_format'    => 'Data final inválida. Use o formato AAAA-MM-DD.',
            'end_date.after_or_equal' => 'A data final deve ser igual ou posterior à data inicial.',
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
        $data = $request->validate([
            'student_id' => ['nullable', 'integer', 'exists:students,id'],
            'period'     => ['nullable', Rule::in(['month', 'custom'])],
            'start_date' => ['nullable', 'date_format:Y-m-d'],
            'end_date'   => ['nullable', 'date_format:Y-m-d', 'after_or_equal:start_date'],
        ]);

        $period = $data['period'] ?? 'month';
        $startDate = $period === 'custom' && !empty($data['start_date'])
            ? $data['start_date']
            : now()->startOfMonth()->toDateString();
        $endDate = $period === 'custom' && !empty($data['end_date'])
            ? $data['end_date']
            : now()->endOfMonth()->toDateString();

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $baseStudentsQuery = Student::with(['user', 'enrollments.plan'])
            ->whereHas('user')
            ->whereHas('enrollments', function ($query) {
                $query->whereIn('status', ['active', 'cancelled'])
                    ->where('end_date', '>=', now()->toDateString());
            });

        if ($user->isInstructor()) {
            $baseStudentsQuery->where('instructor_id', $user->instructor?->id);
        }

        $studentOptions = (clone $baseStudentsQuery)
            ->get()
            ->sortBy(fn ($student) => mb_strtolower($student->user?->name ?? ''))
            ->map(fn (Student $student) => [
                'id' => $student->id,
                'name' => $student->user->name,
            ])
            ->values();

        if (!empty($data['student_id'])) {
            $baseStudentsQuery->whereKey($data['student_id']);
        }

        $enrollments = $baseStudentsQuery
            ->get()
            ->map(function (Student $student) use ($startDate, $endDate) {
                $currentEnrollment = $student->enrollments
                    ->whereIn('status', ['active', 'cancelled'])
                    ->filter(fn ($enrollment) => $enrollment->end_date->gte(now()->startOfDay()))
                    ->sortByDesc('end_date')
                    ->first();

                $fidelity = $student->user->calculateFidelity($startDate, $endDate);
                $fidelityStatus = $fidelity['status'] ?? $fidelity['fidelity_status'] ?? null;

                return [
                    'student_id'      => $student->id,
                    'student_name'    => $student->user->name,
                    'student_email'   => $student->user->email,
                    'plan_name'       => $currentEnrollment?->plan?->name ?? '—',
                    'period_label'    => \Carbon\Carbon::parse($startDate)->format('d/m/Y') . ' até ' . \Carbon\Carbon::parse($endDate)->format('d/m/Y'),
                    'fidelity_rate'   => $fidelity['fidelity_rate'] ?? null,
                    'total_expected'  => $fidelity['total_expected'] ?? 0,
                    'total_present'   => $fidelity['total_present'] ?? 0,
                    'message'         => $fidelity['message'] ?? null,
                    'status'          => $fidelityStatus,
                    'is_low_fidelity' => in_array($fidelityStatus, ['low', 'baixa', 'baixo', 'critical', 'critica'], true),
                ];
            })
            ->sortBy([
                fn ($a, $b) => ($b['fidelity_rate'] ?? -1) <=> ($a['fidelity_rate'] ?? -1),
                fn ($a, $b) => mb_strtolower($a['student_name']) <=> mb_strtolower($b['student_name']),
            ])
            ->values();

        $rates = $enrollments
            ->pluck('fidelity_rate')
            ->filter(fn ($rate) => $rate !== null);

        $summary = [
            'total_students' => $enrollments->count(),
            'average_rate'   => $rates->count() ? round($rates->avg(), 1) : null,
            'best_rate'      => $rates->count() ? round($rates->max(), 1) : null,
            'low_count'      => $enrollments->where('is_low_fidelity', true)->count(),
        ];

        if ($request->expectsJson()) {
            return response()->json([
                'data' => $enrollments,
                'period' => [
                    'start' => $startDate,
                    'end' => $endDate,
                ],
                'summary' => $summary,
            ]);
        }

        return view('reports.plans-loyalty', compact('enrollments', 'studentOptions', 'period', 'startDate', 'endDate', 'summary'));
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
