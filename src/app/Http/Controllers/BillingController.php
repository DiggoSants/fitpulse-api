<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Student;
use App\Models\Billing;

class BillingController extends Controller
{
    public function process(Request $request)
    {
        $request->validate([
            'payment_method' => ['required', 'in:credit_card,pix,boleto'],
        ], [
            'payment_method.required' => 'Informe o método de pagamento',
            'payment_method.in'       => 'Método inválido. Use: credit_card, pix ou boleto',
        ]);

        /** @var \App\Models\User $user */
        $user    = Auth::user();
        $student = Student::where('user_id', $user->id)->firstOrFail();

        $enrollment = $student->activeEnrollment();

        if (!$enrollment) {
            return response()->json([
                'message' => 'Nenhuma matrícula ativa encontrada.',
            ], 422);
        }

        $existingBilling = Billing::where('enrollment_id', $enrollment->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->first();

        if ($existingBilling) {
            return response()->json([
                'message' => 'Já existe um pagamento ' . ($existingBilling->isPending() ? 'pendente' : 'confirmado') . ' para esta matrícula.',
                'data'    => $existingBilling,
            ], 422);
        }

        // ── SIMULAÇÃO DE PAGAMENTO ────────────────────────────────────────────
        // Boleto sempre fica pendente 
        // Pix é sempre confirmado imediatamente
        // Cartão tem 90% de chance de confirmar e 10% de rejeitar
        $status = match ($request->payment_method) {
            'boleto'      => 'pending',
            'pix'         => 'confirmed',
            'credit_card' => (rand(1, 10) <= 9) ? 'confirmed' : 'rejected',
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

            // Se rejeitado, marca o aluno como devedor
            if ($status === 'rejected') {
                $student->update(['is_defaulter' => true]);
            }

            if ($status === 'confirmed') {
                $student->update(['is_defaulter' => false]);
            }

            return $billing;
        });

        $messages = [
            'pending'   => 'Boleto gerado! Aguardando compensação.',
            'confirmed' => 'Pagamento confirmado com sucesso!',
            'rejected'  => 'Pagamento recusado. Verifique seus dados.',
        ];

        return response()->json([
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
        $user    = Auth::user();
        $student = Student::where('user_id', $user->id)->firstOrFail();

        $billings = Billing::with(['plan', 'enrollment'])
            ->where('student_id', $student->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($billing) {
                return [
                    'id'         => $billing->id,
                    'plan_name'  => $billing->plan->name,
                    'amount'     => $billing->amount,
                    'status'     => $billing->status,
                    'paid_at'    => $billing->paid_at?->format('d/m/Y H:i'),
                    'created_at' => $billing->created_at->format('d/m/Y H:i'),
                ];
            });

        return response()->json(['data' => $billings]);
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
}