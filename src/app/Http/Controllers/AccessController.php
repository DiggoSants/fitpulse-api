<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;

class AccessController extends Controller
{
    public function block(Request $request)
    {
        $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'reason'     => ['required', 'in:manual,delinquent'],
        ], [
            'student_id.required' => 'Informe o aluno',
            'student_id.exists'   => 'Aluno não encontrado',
            'reason.required'     => 'Informe o motivo do bloqueio',
            'reason.in'           => 'Motivo inválido. Use: manual ou delinquent',
        ]);

        $student = Student::findOrFail($request->student_id);

        if ($student->isBlocked()) {
            return response()->json([
                'message' => 'Este aluno já está bloqueado.',
            ], 422);
        }

        if ($request->reason === 'delinquent') {
            $student->markDelinquent();
        } else {
            $student->block();
        }

        return response()->json([
            'message' => 'Acesso bloqueado com sucesso.',
            'data'    => [
                'student_id' => $student->id,
                'name'       => $student->user->name,
                'status'     => $student->status,
            ],
        ]);
    }

    public function unblock(Request $request)
    {
        $request->validate([
            'student_id' => ['required', 'exists:students,id'],
        ], [
            'student_id.required' => 'Informe o aluno',
            'student_id.exists'   => 'Aluno não encontrado',
        ]);

        $student = Student::findOrFail($request->student_id);

        if ($student->isActive()) {
            return response()->json([
                'message' => 'Este aluno já está ativo.',
            ], 422);
        }

        if ($student->paymentStatus() === 'pending') {
            return response()->json([
                'message' => 'Aluno possui pagamento pendente. Confirme o pagamento antes de desbloquear.',
            ], 422);
        }

        $student->activate();

        return response()->json([
            'message' => 'Acesso desbloqueado com sucesso.',
            'data'    => [
                'student_id' => $student->id,
                'name'       => $student->user->name,
                'status'     => $student->status,
            ],
        ]);
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'status'     => ['required', 'in:active,blocked,delinquent'],
        ], [
            'student_id.required' => 'Informe o aluno',
            'status.required'     => 'Informe o status',
            'status.in'           => 'Status inválido. Use: active, blocked ou delinquent',
        ]);

        $student = Student::findOrFail($request->student_id);

        $student->update([
            'status'       => $request->status,
            'is_defaulter' => $request->status !== 'active',
        ]);

        return response()->json([
            'message' => 'Status atualizado com sucesso.',
            'data'    => [
                'student_id' => $student->id,
                'name'       => $student->user->name,
                'status'     => $student->status,
                'is_defaulter' => $student->is_defaulter,
            ],
        ]);
    }

   public function students()
{
    $students = Student::with(['user', 'billings' => function ($query) {
        $query->latest()->limit(1);
    }])
    ->whereHas('user', function ($q) {
        $q->whereDoesntHave('manager')
          ->whereDoesntHave('instructor')
          ->whereDoesntHave('receptionist');
    })
    ->get()
   ->map(function ($student) {
    $lastBilling = $student->billings->first();
    return [
        'id'             => $student->id,
        'name'           => $student->user->name,
        'email'          => $student->user->email,
        'status'         => $student->status,
        'is_defaulter'   => $student->is_defaulter,
        'payment_status' => $student->paymentStatus(),
        'payment_amount' => $lastBilling ? number_format($lastBilling->amount, 2, ',', '.') : null,
        'renewed_at'     => $student->renewed_at?->format('d/m/Y H:i'),
    ];
});

    return response()->json(['data' => $students]);
}
}
