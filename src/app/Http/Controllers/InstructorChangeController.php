<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Instructor;
use App\Models\InstructorChangeLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;


class InstructorChangeController extends Controller
{
    /**
     * Retorna lista de instrutores disponíveis para o aluno logado
     */
    public function availableInstructors(Request $request)
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->firstOrFail();
        $shift = $request->get('shift', 'full_day');

        // Busca todos os instrutores ativos que cobrem a agenda do aluno
        $studentDays = $student->user->schedule()->where('active', true)->pluck('week_day')->toArray();
        if (empty($studentDays)) {
            return response()->json([
                'available' => [],
                'message' => 'Defina sua agenda de treino antes de trocar de instrutor.'
            ]);
        }

        $instructors = Instructor::whereHas('availability', function ($q) use ($studentDays, $shift) {
            $q->whereIn('week_day', $studentDays)
              ->where('shift', $shift)
              ->where('active', true);
        })
        ->with('user')
        ->get()
        ->filter(function ($instructor) use ($studentDays, $shift) {
            $availableDays = $instructor->availability()
                ->whereIn('week_day', $studentDays)
                ->where('shift', $shift)
                ->where('active', true)
                ->pluck('week_day')
                ->toArray();
            return count(array_intersect($studentDays, $availableDays)) === count($studentDays);
        })
        ->map(function ($instructor) {
            return [
                'id' => $instructor->id,
                'name' => $instructor->user->name,
                'current_students' => $instructor->students()->count(),
                'max_students' => $instructor->max_students ?? null,
            ];
        })
        ->values();

        return response()->json([
            'available_instructors' => $instructors,
            'current_instructor' => $student->instructor ? [
                'id' => $student->instructor->id,
                'name' => $student->instructor->user->name,
            ] : null,
        ]);
    }

    /**
     * Executa a troca de instrutor
     */
    public function change(Request $request)
    {
        $request->validate([
            'instructor_id' => ['required', 'exists:instructors,id'],
            'shift' => ['nullable', 'string', 'in:morning,afternoon,evening,full_day'],
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->firstOrFail();

        // Impedir troca se o aluno não tiver agenda
        $studentDays = $student->user->schedule()->where('active', true)->pluck('week_day')->toArray();
        if (empty($studentDays)) {
            throw ValidationException::withMessages([
                'instructor_id' => 'Você precisa definir sua agenda de treino antes de trocar de instrutor.'
            ]);
        }

        $newInstructor = Instructor::findOrFail($request->instructor_id);
        $shift = $request->input('shift', 'full_day');

        // Verificar disponibilidade do novo instrutor
        if (!$newInstructor->isAvailableForStudent($student, $shift)) {
            throw ValidationException::withMessages([
                'instructor_id' => 'O instrutor selecionado não está disponível nos dias e horários da sua agenda.'
            ]);
        }

        // Verificar limite de alunos (opcional)
        if ($newInstructor->hasReachedLimit()) {
            throw ValidationException::withMessages([
                'instructor_id' => 'Este instrutor atingiu o limite de alunos. Escolha outro.'
            ]);
        }

        $oldInstructorId = $student->instructor_id;

        // Registrar log antes de alterar
        InstructorChangeLog::create([
            'student_id' => $student->id,
            'old_instructor_id' => $oldInstructorId,
            'new_instructor_id' => $newInstructor->id,
            'changed_by' => $user->id,
            'reason' => $request->reason,
        ]);

        // Atualizar vínculo
        $student->update(['instructor_id' => $newInstructor->id]);

        $message = "Instrutor alterado com sucesso! Agora você está com {$newInstructor->user->name}.";

        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
                'new_instructor' => [
                    'id' => $newInstructor->id,
                    'name' => $newInstructor->user->name,
                ],
            ]);
        }

        return back()->with('success', $message);
    }
}