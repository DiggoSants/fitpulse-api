<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;

/**
 * @method static \Illuminate\Http\JsonResponse show(Request $request)
 */
class FidelityController extends Controller
{
    /**
     * Aluno vê sua própria fidelidade
     */
    public function show(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // O intelephense reconhece com a dica de tipo acima
        if (!$user->isStudent()) {
            return response()->json(['message' => 'Apenas alunos podem acessar.'], 403);
        }

        $startDate = $request->get('start_date');
        $endDate   = $request->get('end_date');

        $fidelity = $user->calculateFidelity($startDate, $endDate);

        return response()->json($fidelity);
    }

    /**
     * Instrutor/gerente vê fidelidade de um aluno
     */
    public function showForInstructor(Request $request, int $studentId)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user->isInstructor() && !$user->isManager()) {
            return response()->json(['message' => 'Acesso negado.'], 403);
        }

        $student = Student::findOrFail($studentId);
        /** @var \App\Models\User $studentUser */
        $studentUser = $student->user;

        // Verifica vínculo
        $instructorId = $user->instructor ? $user->instructor->id : null;
        if (!$user->isManager() && $student->instructor_id !== $instructorId) {
            return response()->json(['message' => 'Este aluno não está vinculado a você.'], 403);
        }

        $startDate = $request->get('start_date');
        $endDate   = $request->get('end_date');

        $fidelity = $studentUser->calculateFidelity($startDate, $endDate);
        $fidelity['student'] = [
            'id'    => $studentUser->id,
            'name'  => $studentUser->name,
            'email' => $studentUser->email,
        ];

        return response()->json($fidelity);
    }

    /**
     * Instrutor vê lista de fidelidade dos seus alunos
     */
    public function listForInstructor(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user->isInstructor()) {
            return response()->json(['message' => 'Apenas instrutores podem ver essa lista.'], 403);
        }

        $instructor = $user->instructor;
        if (!$instructor) {
            return response()->json(['message' => 'Instrutor não encontrado.'], 404);
        }

        $students = Student::with('user')->where('instructor_id', $instructor->id)->get();

        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate   = $request->get('end_date', now()->endOfMonth()->toDateString());

        $result = [];
        foreach ($students as $student) {
            /** @var \App\Models\User $studentUser */
            $studentUser = $student->user;
            $fidelity = $studentUser->calculateFidelity($startDate, $endDate);
            $result[] = [
                'student_id'    => $studentUser->id,
                'student_name'  => $studentUser->name,
                'fidelity_rate' => $fidelity['fidelity_rate'],
                'total_expected'=> $fidelity['total_expected'],
                'total_present' => $fidelity['total_present'],
                'message'       => $fidelity['message'],
            ];
        }

        usort($result, fn($a, $b) => ($a['fidelity_rate'] ?? 100) <=> ($b['fidelity_rate'] ?? 100));

        return response()->json([
            'period'   => ['start' => $startDate, 'end' => $endDate],
            'students' => $result,
        ]);
    }
}