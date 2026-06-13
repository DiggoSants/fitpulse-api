<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Workout;
use App\Models\WorkoutSession;
use App\Models\WorkoutSessionExercise;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WorkoutSessionController extends Controller
{
    public function today()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->isInstructor() || $user->isManager()) {
            return redirect()->route('dashboard');
        }

        $student = Student::where('user_id', $user->id)->firstOrFail();
        $studentSchedule = $student->user->schedule()
            ->where('active', true)
            ->pluck('week_day')
            ->toArray();

        $todayWeekDay = strtolower(now()->englishDayOfWeek);

        if (!in_array($todayWeekDay, $studentSchedule, true)) {
            return view('workout-sessions.no-workout-today', [
                'message' => 'Hoje não é dia de treino de acordo com sua agenda.',
            ]);
        }

        $workout = Workout::where('student_id', $student->id)
            ->where('week_day', $todayWeekDay)
            ->with('workoutExercises.exercise')
            ->latest()
            ->first();

        $hasWorkoutsWithDay = Workout::where('student_id', $student->id)
            ->whereNotNull('week_day')
            ->exists();

        if (!$workout && !$hasWorkoutsWithDay) {
            $workout = Workout::where('student_id', $student->id)
                ->with('workoutExercises.exercise')
                ->latest()
                ->first();
        }

        if (!$workout) {
            return view('workout-sessions.no-workout-today', [
                'message' => 'Não há treino cadastrado para este dia da sua agenda.',
            ]);
        }

        $session = WorkoutSession::firstOrCreate(
            [
                'workout_id' => $workout->id,
                'student_id' => $user->id,
                'session_date' => today(),
            ],
            [
                'status' => 'pending',
                'total_exercises' => $workout->workoutExercises->count(),
                'completed_exercises' => 0,
            ]
        );

        $sessionExercises = $session->sessionExercises;

        if ($sessionExercises->isEmpty()) {
            foreach ($workout->workoutExercises as $workoutExercise) {
                WorkoutSessionExercise::create([
                    'workout_session_id' => $session->id,
                    'workout_exercise_id' => $workoutExercise->id,
                    'completed' => false,
                ]);
            }
        }

        $totalExercises = $workout->workoutExercises()->count();
        $completedExercises = $session->sessionExercises()->where('completed', true)->count();

        if ($session->total_exercises !== $totalExercises || $session->completed_exercises !== $completedExercises) {
            $session->update([
                'total_exercises' => $totalExercises,
                'completed_exercises' => $completedExercises,
            ]);
        }

        $session->load('workout', 'sessionExercises.workoutExercise.exercise');

        if ($session->status === 'completed') {
            return view('workout-sessions.completed', compact('session'));
        }

        $sessionExercises = $session->sessionExercises;

        foreach ($sessionExercises as $sessionExercise) {
            $sessionExercise->exercise = $sessionExercise->workoutExercise->exercise;
            $sessionExercise->sets = $sessionExercise->workoutExercise->sets;
            $sessionExercise->reps = $sessionExercise->workoutExercise->reps;
        }

        return view('workout-sessions.today', compact('session', 'sessionExercises', 'workout'));
    }

    public function start($sessionId)
    {
        $session = WorkoutSession::findOrFail($sessionId);

        abort_if($session->student_id !== Auth::id(), 403);

        if ($session->status !== 'pending') {
            return $this->blockedResponse('Este treino já foi iniciado ou finalizado.');
        }

        $session->update([
            'status' => 'in_progress',
            'started_at' => now(),
        ]);

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Treino iniciado! Bora malhar!',
                'status' => $session->status,
                'started_at' => $session->started_at?->format('H:i'),
            ]);
        }

        return redirect()
            ->route('workout-sessions.today')
            ->with('success', 'Treino iniciado! Bora malhar!');
    }

    public function completeExercise(Request $request, $sessionExerciseId)
    {
        $request->validate([
            'actual_sets' => ['nullable', 'integer', 'min:1'],
            'actual_reps' => ['nullable', 'integer', 'min:1'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $sessionExercise = WorkoutSessionExercise::with('workoutSession')->findOrFail($sessionExerciseId);
        $session = $sessionExercise->workoutSession;

        abort_if($session->student_id !== Auth::id(), 403);

        if ($session->status !== 'in_progress') {
            return response()->json([
                'message' => 'Treino não está em andamento.',
            ], 422);
        }

        if ($sessionExercise->completed) {
            return response()->json([
                'message' => 'Este exercício já foi marcado como concluído.',
            ], 422);
        }

        DB::beginTransaction();

        try {
            $sessionExercise->update([
                'completed' => true,
                'completed_at' => now(),
                'actual_sets' => $request->actual_sets,
                'actual_repetitions' => $request->actual_reps,
                'notes' => $request->notes,
            ]);

            $completedCount = $session->sessionExercises()->where('completed', true)->count();
            $session->update([
                'completed_exercises' => $completedCount,
            ]);

            DB::commit();

            $message = $completedCount === $session->total_exercises
                ? 'Todos os exercícios foram concluídos. Finalize o treino quando estiver pronto.'
                : 'Exercício marcado como concluído.';

            return response()->json([
                'success' => true,
                'message' => $message,
                'completed_count' => $completedCount,
                'total_count' => $session->total_exercises,
                'progress' => $session->progress_percentage,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Erro ao marcar exercício. Tente novamente.',
            ], 500);
        }
    }

    public function complete($sessionId)
    {
        $session = WorkoutSession::findOrFail($sessionId);

        abort_if($session->student_id !== Auth::id(), 403);

        if ($session->status !== 'in_progress') {
            return $this->blockedResponse('Treino não está em andamento.');
        }

        if ($session->completed_exercises < $session->total_exercises) {
            $pending = $session->total_exercises - $session->completed_exercises;
            return $this->blockedResponse("Você ainda tem $pending exercício(s) pendente(s). Complete todos para finalizar.", [
                'pending' => $pending,
            ]);
        }

        $session->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Treino finalizado com sucesso! Parabéns!',
                'status' => $session->status,
                'completed_at' => $session->completed_at?->format('H:i'),
            ]);
        }

        return redirect()
            ->route('workout-sessions.today')
            ->with('success', 'Treino finalizado com sucesso! Parabéns!');
    }

    public function history()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $sessions = WorkoutSession::where('student_id', $user->id)
            ->with('workout')
            ->orderBy('session_date', 'desc')
            ->paginate(10);

        $stats = [
            'total_workouts' => $sessions->total(),
            'completed_workouts' => WorkoutSession::where('student_id', $user->id)
                ->where('status', 'completed')
                ->count(),
            'total_exercises_done' => WorkoutSessionExercise::whereHas('workoutSession', function ($query) use ($user) {
                $query->where('student_id', $user->id);
            })->where('completed', true)->count(),
        ];

        return view('workout-sessions.history', compact('sessions', 'stats'));
    }

    public function getExerciseDetails($sessionExerciseId)
    {
        $sessionExercise = WorkoutSessionExercise::with([
            'workoutSession',
            'workoutExercise.exercise',
        ])->findOrFail($sessionExerciseId);

        abort_if($sessionExercise->workoutSession->student_id !== Auth::id(), 403);

        return response()->json([
            'id' => $sessionExercise->id,
            'name' => $sessionExercise->workoutExercise->exercise->name,
            'muscle_group' => $sessionExercise->workoutExercise->exercise->muscle_group,
            'expected_sets' => $sessionExercise->workoutExercise->sets,
            'expected_reps' => $sessionExercise->workoutExercise->reps,
            'completed' => $sessionExercise->completed,
            'actual_sets' => $sessionExercise->actual_sets,
            'actual_repetitions' => $sessionExercise->actual_repetitions,
            'notes' => $sessionExercise->notes,
        ]);
    }

    private function blockedResponse(string $message, array $extra = [])
    {
        if (request()->expectsJson()) {
            return response()->json(array_merge([
                'message' => $message,
            ], $extra), 422);
        }

        return back()->with('error', $message);
    }
}
