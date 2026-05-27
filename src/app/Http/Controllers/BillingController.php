<?php

namespace App\Http\Controllers;

use App\Models\Billing;
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
            'payment_method' => ['required', 'in:credit_card,pix,boleto'],
        ], [
            'payment_method.required' => 'Informe o metodo de pagamento',
            'payment_method.in'       => 'Metodo invalido. Use: credit_card, pix ou boleto',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->firstOrFail();

        $enrollment = $student->activeEnrollment();

        if (!$enrollment) {
            return $this->billingResponse($request, [
                'message' => 'Nenhuma matricula ativa encontrada.',
            ], 422);
        }

        [$billing, $wasCreated] = DB::transaction(function () use ($student, $enrollment, $request, $billingService) {
            Student::whereKey($student->id)->lockForUpdate()->firstOrFail();

            $existingBilling = Billing::where('enrollment_id', $enrollment->id)
                ->whereIn('status', ['pending', 'confirmed'])
                ->lockForUpdate()
                ->first();

            if ($existingBilling) {
                return [$existingBilling, false];
            }

            return [
                $billingService->createForEnrollment(
                    $student,
                    $enrollment,
                    $request->payment_method
                ),
                true,
            ];
        });

        if (!$wasCreated) {
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
        ], 201);
    }

    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->firstOrFail();

        $activeEnrollment = $student->activeEnrollment();
        $payments = Billing::with(['plan', 'enrollment'])
            ->where('student_id', $student->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('billing.index', compact('activeEnrollment', 'payments'));
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
}
