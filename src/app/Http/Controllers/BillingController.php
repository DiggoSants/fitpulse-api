<?php

namespace App\Http\Controllers;

use App\Models\Billing;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BillingController extends Controller
{
    public function process(Request $request)
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

        $existingBilling = Billing::where('enrollment_id', $enrollment->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->first();

        if ($existingBilling) {
            return $this->billingResponse($request, [
                'message' => 'Ja existe um pagamento ' . ($existingBilling->isPending() ? 'pendente' : 'confirmado') . ' para esta matricula.',
                'data'    => $existingBilling,
            ], 422);
        }

        $status = match ($request->payment_method) {
            'boleto'      => 'pending',
            'pix'         => 'confirmed',
            'credit_card' => rand(1, 10) <= 9 ? 'confirmed' : 'rejected',
        };

        $billing = DB::transaction(function () use ($student, $enrollment, $status) {
            $billing = Billing::create([
                'student_id'    => $student->id,
                'plan_id'       => $enrollment->plan_id,
                'enrollment_id' => $enrollment->id,
                'amount'        => $enrollment->plan->price,
                'status'        => $status,
                'paid_at'       => $status === 'confirmed' ? now() : null,
            ]);

            if ($status === 'rejected') {
                $student->update(['is_defaulter' => true]);
            }

            if ($status === 'confirmed') {
                $student->update(['is_defaulter' => false]);

                if ($student->isDelinquent()) {
                    $student->activate();
                }
            }

            return $billing;
        });

        $messages = [
            'pending'   => 'Boleto gerado! Aguardando compensacao.',
            'confirmed' => 'Pagamento confirmado com sucesso!',
            'rejected'  => 'Pagamento recusado. Verifique seus dados.',
        ];

        return $this->billingResponse($request, [
            'message' => $messages[$status],
            'data'    => [
                'billing_id'     => $billing->id,
                'amount'         => $billing->amount,
                'status'         => $billing->status,
                'paid_at'        => $billing->paid_at?->format('d/m/Y H:i'),
                'payment_method' => $request->payment_method,
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
