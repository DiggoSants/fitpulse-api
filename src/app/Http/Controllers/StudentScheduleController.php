<?php

namespace App\Http\Controllers;

use App\Models\StudentSchedule;
use App\Models\Student;
use App\Models\User;
use App\Http\Requests\StudentScheduleRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentScheduleController extends Controller
{
    private function resolveTargetUser(Request $request, ?int $userId = null): User
    {
        /** @var \App\Models\User $authUser */
        $authUser = Auth::user();

        $studentId    = $request->input('student_id');
        $targetUserId = $userId ?? $request->input('user_id');

        if (!$studentId && !$targetUserId) {
            return $authUser;
        }

        if (!$authUser->isInstructor() && !$authUser->isManager()) {
            abort_if((int) $targetUserId !== $authUser->id, 403);
            return $authUser;
        }

        $student = $studentId
            ? Student::with('user')->findOrFail((int) $studentId)
            : Student::with('user')->where('user_id', (int) $targetUserId)->firstOrFail();

        if ($authUser->isInstructor()) {
            $instructor = $authUser->instructor;

            if ($student->instructor_id === null) {
                $student->forceFill(['instructor_id' => $instructor->id])->save();
            }

            abort_if($student->instructor_id !== $instructor->id, 403, 'Voce nao pode alterar a agenda deste aluno.');
        }

        return $student->user;
    }

    public function store(StudentScheduleRequest $request)
    {
        $user = $this->resolveTargetUser($request);

        if (!$user) {
            return response()->json([
                'message' => 'Usuário não está autenticado.',
            ], 401);
        }

        // Quando nenhum checkbox é enviado, days não vem no POST — tratamos como array vazio
        $days = $request->input('days', []);
        $requestedShifts = $request->input('shifts', []);
        $existingShifts = StudentSchedule::where('user_id', $user->id)
            ->pluck('shift', 'week_day')
            ->toArray();

        if (count($days) < StudentSchedule::MIN_DAYS) {
            $error = 'Selecione pelo menos ' . StudentSchedule::MIN_DAYS . ' dias de treino na semana.';

            if ($request->wantsJson()) {
                return response()->json(['message' => $error], 422);
            }

            return back()->withErrors(['days' => $error])->withInput();
        }

        // Remove todos os dias anteriores e recria
        StudentSchedule::where('user_id', $user->id)->delete();

        foreach ($days as $day) {
            StudentSchedule::create([
                'user_id'  => $user->id,
                'week_day' => $day,
                'shift'    => $requestedShifts[$day] ?? $existingShifts[$day] ?? 'full_day',
                'active'   => true,
            ]);
        }

        $payload = [
            'message'    => 'Agenda salva com sucesso!',
            'days'       => $days,
            'shifts'     => collect($days)
                ->mapWithKeys(fn ($day) => [$day => $requestedShifts[$day] ?? $existingShifts[$day] ?? 'full_day'])
                ->all(),
            'total_days' => count($days),
        ];

        if ($request->wantsJson()) {
            return response()->json($payload);
        }

        return back()
            ->with('success', $payload['message'])
            ->with('schedule_user_id', $user->id);
    }

    public function show($userId = null)
    {
        if ($userId === null) {
            $userId = Auth::id();
        }

        $user   = $this->resolveTargetUser(request(), (int) $userId);
        $userId = $user->id;

        $scheduleRows = StudentSchedule::where('user_id', $userId)
            ->where('active', true)
            ->get(['week_day', 'shift']);
        $schedule = $scheduleRows
            ->pluck('week_day')
            ->toArray();

        $weekDaysMap = StudentSchedule::weekDays();

        return response()->json([
            'days'           => $schedule,
            'shifts'         => $scheduleRows
                ->pluck('shift', 'week_day')
                ->map(fn ($shift) => $shift ?: 'full_day')
                ->toArray(),
            'total_days'     => count($schedule),
            'formatted_days' => array_map(fn ($day) => $weekDaysMap[$day] ?? $day, $schedule),
        ]);
    }

    public function validateSchedule($userId)
    {
        $user   = $this->resolveTargetUser(request(), (int) $userId);
        $userId = $user->id;

        $totalDays = StudentSchedule::where('user_id', $userId)
            ->where('active', true)
            ->count();

        if ($totalDays < StudentSchedule::MIN_DAYS) {
            return response()->json([
                'valid'   => false,
                'message' => 'Aluno precisa ter pelo menos ' . StudentSchedule::MIN_DAYS . ' dias de treino na semana.',
            ], 422);
        }

        return response()->json([
            'valid'      => true,
            'total_days' => $totalDays,
        ]);
    }
}
