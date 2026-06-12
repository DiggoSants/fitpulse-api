<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Workout;
use App\Models\WorkoutSession;
use App\Models\WorkoutSessionExercise;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WorkoutSessionController extends Controller
{
    // Mostrar treino do dia para o aluno
    public function today()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        if ($user->isInstructor() || $user->isManager()) {
            return redirect()->route('dashboard');
        }
        
        $student = Student::where('user_id', $user->id)->firstOrFail();
        
        // Buscar agenda do aluno
        $studentSchedule = $student->user->schedule()
            ->where('active', true)
            ->pluck('week_day')
            ->toArray();
        $today = strtolower(now()->englishDayOfWeek);
        
        $weekDaysMap = [
            'monday' => 'monday',
            'tuesday' => 'tuesday',
            'wednesday' => 'wednesday',
            'thursday' => 'thursday',
            'friday' => 'friday',
            'saturday' => 'saturday',
            'sunday' => 'sunday'
        ];
        
        $todayWeekDay = $weekDaysMap[$today] ?? null;
        
        // Verificar se aluno treina hoje
        if (!in_array($todayWeekDay, $studentSchedule)) {
            return view('workout-sessions.no-workout-today', [
                'message' => 'Hoje não é dia de treino de acordo com sua agenda.'
            ]);
        }
        
        // Buscar treino ativo do aluno para o dia definido na agenda
        $workout = Workout::where('student_id', $student->id)
            ->where('week_day', $todayWeekDay)
            ->latest()
            ->first();

        $hasWorkoutsWithDay = Workout::where('student_id', $student->id)
            ->whereNotNull('week_day')
            ->exists();

        if (!$workout && !$hasWorkoutsWithDay) {
            $workout = Workout::where('student_id', $student->id)
                ->latest()
                ->first();
        }
        
        if (!$workout) {
            return view('workout-sessions.no-workout-today', [
                'message' => 'Não há treino cadastrado para este dia da sua agenda.'
            ]);
        }
        
        // Buscar ou criar sessão de hoje
        $session = WorkoutSession::firstOrCreate(
            [
                'workout_id' => $workout->id,
                'student_id' => $user->id,
                'session_date' => today(),
            ],
            [
                'status' => 'pending',
                'total_exercises' => $workout->workoutExercises->count(),
                'completed_exercises' => 0
            ]
        );
        
        // Se já está completo, redirecionar
        if ($session->status === 'completed') {
            return view('workout-sessions.completed', compact('session'));
        }
        
        // Buscar exercícios da sessão
        $sessionExercises = $session->sessionExercises;
        
        // Se não tem exercícios na sessão, criar
        if ($sessionExercises->isEmpty()) {
            foreach ($workout->workoutExercises as $we) {
                WorkoutSessionExercise::create([
                    'workout_session_id' => $session->id,
                    'workout_exercise_id' => $we->id,
                    'completed' => false
                ]);
            }
            $sessionExercises = $session->sessionExercises;
        }
        
        // Carregar dados dos exercícios
        foreach ($sessionExercises as $se) {
            $se->exercise = $se->workoutExercise->exercise;
            $se->sets = $se->workoutExercise->sets;
            $se->reps = $se->workoutExercise->reps;
        }
        
        return view('workout-sessions.today', compact('session', 'sessionExercises', 'workout'));
    }
    
    // Iniciar treino
    public function start($sessionId)
    {
        $session = WorkoutSession::findOrFail($sessionId);
        
        abort_if($session->student_id !== Auth::id(), 403);
        
        if ($session->status !== 'pending') {
            return back()->with('error', 'Este treino já foi iniciado ou finalizado.');
        }
        
        $session->update([
            'status' => 'in_progress',
            'started_at' => now()
        ]);
        
        return redirect()->route('workout-sessions.today')
            ->with('success', 'Treino iniciado! Bora malhar! 💪');
    }
    
    // Marcar exercício como completo
    public function completeExercise(Request $request, $sessionExerciseId)
    {
        $request->validate([
            'actual_sets' => ['nullable', 'integer', 'min:1'],
            'actual_reps' => ['nullable', 'integer', 'min:1'],
            'notes' => ['nullable', 'string', 'max:500']
        ]);
        
        $sessionExercise = WorkoutSessionExercise::with('workoutSession')->findOrFail($sessionExerciseId);
        $session = $sessionExercise->workoutSession;
        
        abort_if($session->student_id !== Auth::id(), 403);
        
        if ($session->status !== 'in_progress') {
            return response()->json([
                'error' => 'Treino não está em andamento.'
            ], 422);
        }
        
        if ($sessionExercise->completed) {
            return response()->json([
                'error' => 'Este exercício já foi marcado como completo.'
            ], 422);
        }
        
        DB::beginTransaction();
        
        try {
            $sessionExercise->update([
                'completed' => true,
                'completed_at' => now(),
                'actual_sets' => $request->actual_sets,
                'actual_repetitions' => $request->actual_reps,
                'notes' => $request->notes
            ]);
            
            // Atualizar contador de exercícios completos
            $completedCount = $session->sessionExercises()->where('completed', true)->count();
            $session->update([
                'completed_exercises' => $completedCount
            ]);
            
            DB::commit();
            
            $message = $completedCount == $session->total_exercises 
                ? 'Parabéns! Você completou todos os exercícios! Finalize o treino.' 
                : 'Exercício marcado como completo! Continue assim! 🎯';
            
            return response()->json([
                'success' => true,
                'message' => $message,
                'completed_count' => $completedCount,
                'total_count' => $session->total_exercises,
                'progress' => $session->progress_percentage
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Erro ao marcar exercício. Tente novamente.'
            ], 500);
        }
    }
    
    // Finalizar treino
    public function complete($sessionId)
    {
        $session = WorkoutSession::findOrFail($sessionId);
        
        abort_if($session->student_id !== Auth::id(), 403);
        
        if ($session->status !== 'in_progress') {
            return back()->with('error', 'Treino não está em andamento.');
        }
        
        if ($session->completed_exercises < $session->total_exercises) {
            $pending = $session->total_exercises - $session->completed_exercises;
            return back()->with('error', "Você ainda tem $pending exercício(s) pendente(s). Complete todos para finalizar.");
        }
        
        $session->update([
            'status' => 'completed',
            'completed_at' => now()
        ]);
        
        return redirect()->route('workout-sessions.today')
            ->with('success', 'Treino finalizado com sucesso! Parabéns! 🏆');
    }
    
    // Histórico de treinos do aluno
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
                ->where('status', 'completed')->count(),
            'total_exercises_done' => WorkoutSessionExercise::whereHas('workoutSession', function($q) use ($user) {
                $q->where('student_id', $user->id);
            })->where('completed', true)->count(),
        ];
        
        return view('workout-sessions.history', compact('sessions', 'stats'));
    }
    
    // API: Buscar detalhes do exercício (para modal)
    public function getExerciseDetails($sessionExerciseId)
    {
        $sessionExercise = WorkoutSessionExercise::with([
            'workoutSession',
            'workoutExercise.exercise'
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
}
