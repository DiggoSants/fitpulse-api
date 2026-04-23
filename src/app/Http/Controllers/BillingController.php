<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Student;
use App\Models\Billing;

class BillingController extends Controller
{
    // Exibe a tela de mensalidade com plano ativo e histórico de pagamentos
public function index()
{
    $user    = Auth::user();
    $student = Student::where('user_id', $user->id)->firstOrFail();

    // Auto-sync: tem billing confirmado mas status ainda é delinquent
    if ($student->isDelinquent()) {
        $hasConfirmed = $student->billings()
            ->where('status', 'confirmed')
            ->exists();

        if ($hasConfirmed) {
            $student->update([
                'status'       => 'active',
                'is_defaulter' => false,
            ]);
        }
    }

    $activeEnrollment = $student->activeEnrollment();

        $payments = Billing::with(['plan', 'enrollment'])
            ->where('student_id', $student->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('billing.index', compact('activeEnrollment', 'payments'));
    }

    // Processa o pagamento e redireciona de volta com mensagem
    public function process(Request $request)
    {
        $request->validate([
            'payment_method' => ['required', 'in:credit_card,pix,boleto,card'],
            'enrollment_id'  => ['required', 'exists:enrollments,id'],
        ], [
            'payment_method.required' => 'Informe o método de pagamento.',
            'payment_method.in'       => 'Método inválido.',
            'enrollment_id.required'  => 'Matrícula não identificada.',
        ]);

        /** @var \App\Models\User $user */
        $user    = Auth::user();
        $student = Student::where('user_id', $user->id)->firstOrFail();

        $enrollment = $student->activeEnrollment();

        if (!$enrollment || $enrollment->id != $request->enrollment_id) {
            return back()->with('error', 'Nenhuma matrícula ativa encontrada.');
        }

        $existingBilling = Billing::where('enrollment_id', $enrollment->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->first();

        if ($existingBilling) {
            $label = $existingBilling->status === 'pending' ? 'pendente' : 'confirmado';
            return back()->with('error', "Já existe um pagamento {$label} para esta matrícula.");
        }

        // Simulação de pagamento
        $method = $request->payment_method;
        $status = match ($method) {
            'boleto'                   => 'pending',
            'pix'                      => 'confirmed',
            'credit_card', 'card'      => (rand(1, 10) <= 9) ? 'confirmed' : 'rejected',
            default                    => 'pending',
        };

        DB::transaction(function () use ($student, $enrollment, $status, $method) {
            $billing = Billing::create([
                'student_id'    => $student->id,
                'plan_id'       => $enrollment->plan_id,
                'enrollment_id' => $enrollment->id,
                'amount'        => $enrollment->plan->price,
                'status'        => $status,
                'payment_method'=> $method,
                'paid_at'       => $status === 'confirmed' ? now() : null,
            ]);

            if ($status === 'rejected') {
                $student->update(['is_defaulter' => true]);
            }

            if ($status === 'confirmed') {
                $student->update([
                  'is_defaulter' => false,
                  'status'       => 'active',
                   'renewed_at'   => now(),
    ]);
}
        });

        $messages = [
            'pending'   => 'Boleto gerado! Aguardando compensação.',
            'confirmed' => 'Pagamento confirmado com sucesso!',
            'rejected'  => 'Pagamento recusado. Verifique seus dados.',
        ];

        return redirect()->route('billing.index')->with('success', $messages[$status]);
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