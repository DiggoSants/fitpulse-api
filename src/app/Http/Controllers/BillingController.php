<?php

namespace App\Http\Controllers;

use App\Models\Billing;
use App\Models\Enrollment;
use App\Models\Student;
use App\Services\BillingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BillingController extends Controller
{
    public function process(Request $request, BillingService $billingService)
    {
        $request->validate([
            'payment_method' => ['required', 'in:credit_card,debit_card,pix'],
            'enrollment_id'  => ['nullable', 'integer', 'exists:enrollments,id'],
        ], [
            'payment_method.required' => 'Informe o metodo de pagamento',
            'payment_method.in'       => 'Metodo invalido. Use: credit_card, debit_card ou pix',
            'enrollment_id.exists'    => 'Matricula invalida.',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->firstOrFail();

        $enrollment = $this->payableEnrollmentFor($student, $request->integer('enrollment_id'));

        if (!$enrollment) {
            return $this->billingResponse($request, [
                'message' => 'Nao ha mensalidade pendente para pagar.',
            ], 422);
        }

        [$billing, $action] = DB::transaction(function () use ($student, $enrollment, $request, $billingService) {
            Student::whereKey($student->id)->lockForUpdate()->firstOrFail();

            $existingBilling = Billing::where('enrollment_id', $enrollment->id)
                ->whereIn('status', ['pending', 'confirmed'])
                ->lockForUpdate()
                ->first();

            if ($existingBilling?->isConfirmed()) {
                return [$existingBilling, 'existing'];
            }

            if ($existingBilling?->isPending()) {
                return [
                    $billingService->applyPaymentToBilling(
                        $existingBilling,
                        $student,
                        $request->payment_method
                    ),
                    'updated',
                ];
            }

            return [
                $billingService->createForEnrollment(
                    $student,
                    $enrollment,
                    $request->payment_method
                ),
                'created',
            ];
        });

        if ($action === 'existing') {
            return $this->billingResponse($request, [
                'message' => 'Ja existe um pagamento ' . ($billing->isPending() ? 'pendente' : 'confirmado') . ' para esta matricula.',
                'data'    => $billing,
            ], 422);
        }

        return $this->billingResponse($request, [
            'message' => $billingService->messageForStatus($billing->status),
            'data'    => [
                'billing_id'     => $billing->id,
                'amount'         => $billing->amount,
                'status'         => $billing->status,
                'paid_at'        => $billing->paid_at?->format('d/m/Y H:i'),
                'payment_method' => $billing->payment_method,
            ],
        ], $action === 'created' ? 201 : 200);
    }

    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->firstOrFail();

        $activeEnrollment = $this->payableEnrollmentFor($student) ?? $student->activeEnrollment();
        $canPayEnrollment = $activeEnrollment && !$this->hasConfirmedBilling($activeEnrollment);
        $payments = Billing::with(['plan', 'enrollment'])
            ->where('student_id', $student->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('billing.index', compact('activeEnrollment', 'payments', 'canPayEnrollment'));
    }

    public function all(Request $request)
    {
        $query = Billing::with(['student.user', 'plan']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $billings = $query->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($billing) {
                return [
                    'id'           => $billing->id,
                    'student_name' => $billing->student->user->name,
                    'plan_name'    => $billing->plan->name,
                    'amount'       => $billing->amount,
                    'status'       => $billing->status,
                    'payment_method' => $billing->payment_method,
                    'paid_at'      => $billing->paid_at?->format('d/m/Y H:i'),
                    'created_at'   => $billing->created_at->format('d/m/Y H:i'),
                ];
            });

        return response()->json([
            'data'    => $billings,
            'filters' => ['status' => $request->status],
        ]);
    }

    private function billingResponse(Request $request, array $payload, int $status)
    {
        if ($request->expectsJson()) {
            return response()->json($payload, $status);
        }

        if ($status >= 400) {
            return back()
                ->withInput()
                ->with('error', $payload['message'] ?? 'Erro ao processar pagamento.');
        }

        return redirect()
            ->route('billing.index')
            ->with('success', $payload['message'] ?? 'Pagamento processado com sucesso.');
    }

    private function payableEnrollmentFor(Student $student, ?int $requestedEnrollmentId = null): ?Enrollment
    {
        if ($requestedEnrollmentId) {
            $requestedEnrollment = $student->enrollments()
                ->with('plan')
                ->whereKey($requestedEnrollmentId)
                ->whereIn('status', ['active', 'cancelled'])
                ->where('end_date', '>=', now()->toDateString())
                ->first();

            if ($requestedEnrollment && !$this->hasConfirmedBilling($requestedEnrollment)) {
                return $requestedEnrollment;
            }
        }

        $pendingBilling = Billing::with('enrollment.plan')
            ->where('student_id', $student->id)
            ->where('status', 'pending')
            ->whereHas('enrollment', function ($query) {
                $query->whereIn('status', ['active', 'cancelled'])
                    ->where('end_date', '>=', now()->toDateString());
            })
            ->get()
            ->filter(fn ($billing) => $billing->enrollment !== null)
            ->sortBy(fn ($billing) => $billing->enrollment->start_date?->format('Y-m-d') . '-' . str_pad((string) $billing->id, 10, '0', STR_PAD_LEFT))
            ->first();

        if ($pendingBilling?->enrollment) {
            return $pendingBilling->enrollment;
        }

        return $student->enrollments()
            ->with('plan')
            ->whereIn('status', ['active', 'cancelled'])
            ->where('end_date', '>=', now()->toDateString())
            ->get()
            ->filter(fn ($enrollment) => !$this->hasConfirmedBilling($enrollment))
            ->sortBy(fn ($enrollment) => $enrollment->start_date?->format('Y-m-d') . '-' . str_pad((string) $enrollment->id, 10, '0', STR_PAD_LEFT))
            ->first();
    }

    private function hasConfirmedBilling(Enrollment $enrollment): bool
    {
        return Billing::where('enrollment_id', $enrollment->id)
            ->where('status', 'confirmed')
            ->exists();
    }
}
