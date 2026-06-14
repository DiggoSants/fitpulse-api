<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Instructor;
use App\Models\InstructorAvailability;
use App\Models\InstructorChangeLog;
use App\Models\StudentSchedule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class InstructorChangeController extends Controller
{
    /**
     * Retorna lista de instrutores disponíveis para o aluno logado,
     * com os turnos/horários correspondentes à agenda do aluno.
     */
    public function availableInstructors(Request $request)
    {
        $user    = Auth::user();
        $student = Student::where('user_id', $user->id)
            ->with(['instructor.user', 'instructor.availability'])
            ->firstOrFail();

        $studentSchedule = StudentSchedule::where('user_id', $user->id)
            ->where('active', true)
            ->whereNotNull('shift')
            ->get(['week_day', 'shift']);

        if ($studentSchedule->isEmpty()) {
            return response()->json([
                'available_instructors' => [],
                'current_instructor'    => $this->formatCurrentInstructor($student),
                'message'               => 'Defina sua agenda de treino antes de trocar de instrutor.',
            ]);
        }

        $shiftLabels   = InstructorAvailability::shiftLabels();
        $weekDayLabels = InstructorAvailability::weekDaysLabels();

        // Slots que o aluno precisa cobrir
        $requiredSlots = $studentSchedule->map(fn ($s) => [
            'week_day' => $s->week_day,
            'shift'    => $s->shift,
        ])->toArray();

        $occupiedSlots = $this->occupiedSlotsByInstructor($student->id);

        $instructors = Instructor::with(['user:id,name', 'availability' => fn ($q) => $q->where('active', true)])
            ->withCount('students')
            ->get()
            ->filter(function (Instructor $instructor) use ($requiredSlots, $occupiedSlots) {
                foreach ($requiredSlots as $slot) {
                    $hasAvailability = $instructor->availability->contains(
                        fn ($a) => $a->week_day === $slot['week_day'] && $a->shift === $slot['shift']
                    );

                    if (!$hasAvailability) return false;

                    $occupied = $occupiedSlots[$instructor->id][$slot['week_day']] ?? [];
                    if (in_array($slot['shift'], $occupied, true)) return false;
                }

                return true;
            })
            ->sortBy(fn ($i) => [$i->students_count, $i->user?->name])
            ->map(function (Instructor $instructor) use ($requiredSlots, $shiftLabels, $weekDayLabels) {
                // Montar lista de slots relevantes para exibição
                $slots = collect($requiredSlots)->map(function ($slot) use ($instructor, $shiftLabels, $weekDayLabels) {
                    $availability = $instructor->availability->first(
                        fn ($a) => $a->week_day === $slot['week_day'] && $a->shift === $slot['shift']
                    );

                    $timeLabel = $availability
                        ? $this->availabilityTimeLabel($availability, $shiftLabels)
                        : ($shiftLabels[$slot['shift']] ?? $slot['shift']);

                    return [
                        'week_day'       => $slot['week_day'],
                        'week_day_label' => $weekDayLabels[$slot['week_day']] ?? $slot['week_day'],
                        'shift'          => $slot['shift'],
                        'shift_label'    => $shiftLabels[$slot['shift']] ?? $slot['shift'],
                        'time_label'     => $timeLabel,
                    ];
                })->values()->all();

                return [
                    'id'               => $instructor->id,
                    'name'             => $instructor->user?->name ?? 'Instrutor',
                    'specialty'        => $instructor->specialty,
                    'students_count'   => $instructor->students_count,
                    'slots'            => $slots,
                ];
            })
            ->values();

        return response()->json([
            'available_instructors' => $instructors,
            'current_instructor'    => $this->formatCurrentInstructor($student),
        ]);
    }

    /**
     * Executa a troca de instrutor com validação completa de agenda.
     */
    public function change(Request $request)
    {
        $request->validate([
            'instructor_id' => ['required', 'exists:instructors,id'],
            'reason'        => ['nullable', 'string', 'max:500'],
        ], [
            'instructor_id.required' => 'Selecione um instrutor.',
            'instructor_id.exists'   => 'Instrutor inválido.',
        ]);

        $user    = Auth::user();
        $student = Student::where('user_id', $user->id)->firstOrFail();

        $studentSchedule = StudentSchedule::where('user_id', $user->id)
            ->where('active', true)
            ->whereNotNull('shift')
            ->get(['week_day', 'shift']);

        if ($studentSchedule->isEmpty()) {
            throw ValidationException::withMessages([
                'instructor_id' => 'Você precisa ter uma agenda de treino ativa para trocar de instrutor.',
            ]);
        }

        $newInstructor = Instructor::with(['user', 'availability' => fn ($q) => $q->where('active', true)])
            ->findOrFail($request->instructor_id);

        if ((int) $newInstructor->id === (int) $student->instructor_id) {
            throw ValidationException::withMessages([
                'instructor_id' => 'Você já está vinculado a este instrutor.',
            ]);
        }

        $requiredSlots = $studentSchedule->map(fn ($s) => [
            'week_day' => $s->week_day,
            'shift'    => $s->shift,
        ])->toArray();

        $occupiedSlots = $this->occupiedSlotsByInstructor($student->id);

        foreach ($requiredSlots as $slot) {
            $hasAvailability = $newInstructor->availability->contains(
                fn ($a) => $a->week_day === $slot['week_day'] && $a->shift === $slot['shift']
            );

            if (!$hasAvailability) {
                $shiftLabels   = InstructorAvailability::shiftLabels();
                $weekDayLabels = InstructorAvailability::weekDaysLabels();
                $dayLabel      = $weekDayLabels[$slot['week_day']] ?? $slot['week_day'];
                $shiftLabel    = $shiftLabels[$slot['shift']] ?? $slot['shift'];

                throw ValidationException::withMessages([
                    'instructor_id' => "O instrutor selecionado não tem disponibilidade na {$dayLabel} ({$shiftLabel}).",
                ]);
            }

            $occupied = $occupiedSlots[$newInstructor->id][$slot['week_day']] ?? [];
            if (in_array($slot['shift'], $occupied, true)) {
                throw ValidationException::withMessages([
                    'instructor_id' => 'O instrutor selecionado já está ocupado em um dos seus horários de treino.',
                ]);
            }
        }

        if ($newInstructor->hasReachedLimit()) {
            throw ValidationException::withMessages([
                'instructor_id' => 'Este instrutor atingiu o limite máximo de alunos. Escolha outro.',
            ]);
        }

        $oldInstructorId = $student->instructor_id;

        InstructorChangeLog::create([
            'student_id'        => $student->id,
            'old_instructor_id' => $oldInstructorId,
            'new_instructor_id' => $newInstructor->id,
            'changed_by'        => $user->id,
            'reason'            => $request->reason,
        ]);

        $student->update(['instructor_id' => $newInstructor->id]);

        $message = "Instrutor alterado com sucesso! Agora você está com {$newInstructor->user->name}.";

        if ($request->expectsJson()) {
            return response()->json([
                'message'         => $message,
                'new_instructor'  => [
                    'id'        => $newInstructor->id,
                    'name'      => $newInstructor->user->name,
                    'specialty' => $newInstructor->specialty,
                ],
            ]);
        }

        return back()->with('success', $message);
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    private function formatCurrentInstructor(Student $student): ?array
    {
        if (!$student->instructor) return null;

        return [
            'id'        => $student->instructor->id,
            'name'      => $student->instructor->user?->name,
            'specialty' => $student->instructor->specialty,
        ];
    }

    private function occupiedSlotsByInstructor(?int $ignoredStudentId = null): array
    {
        $rows = StudentSchedule::query()
            ->join('users', 'users.id', '=', 'student_schedules.user_id')
            ->join('students', 'students.user_id', '=', 'users.id')
            ->where('student_schedules.active', true)
            ->whereNotNull('students.instructor_id')
            ->whereNotNull('student_schedules.shift')
            ->when($ignoredStudentId, fn ($q) => $q->where('students.id', '<>', $ignoredStudentId))
            ->get(['students.instructor_id', 'student_schedules.week_day', 'student_schedules.shift']);

        $slots = [];

        foreach ($rows as $row) {
            $instructorId = (int) $row->instructor_id;
            $weekDay      = (string) $row->week_day;
            $shift        = (string) $row->shift;

            $slots[$instructorId][$weekDay]   ??= [];
            $slots[$instructorId][$weekDay][]   = $shift;
            $slots[$instructorId][$weekDay]     = array_values(array_unique($slots[$instructorId][$weekDay]));
        }

        return $slots;
    }

    private function availabilityTimeLabel(object $availability, array $shiftLabels): string
    {
        if (!empty($availability->start_time) && !empty($availability->end_time)) {
            return mb_substr((string) $availability->start_time, 0, 5)
                . ' às '
                . mb_substr((string) $availability->end_time, 0, 5);
        }

        return $shiftLabels[$availability->shift] ?? $availability->shift;
    }
}