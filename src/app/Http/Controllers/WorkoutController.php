<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Workout;
use App\Models\Exercise;
use App\Models\WorkoutExercise;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;

class WorkoutController extends Controller
{
    private function studentWorkoutNotice()
    {
        return redirect()->route('workouts.index')
            ->with('info', 'Seu treino fica sob cuidado da equipe. Se quiser mudar alguma coisa, fale com seu instrutor ou com a recepção.');
    }

    private function resolveStudent(?int $studentId = null): Student
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($studentId && ($user->isInstructor() || $user->isManager())) {
            $student = Student::findOrFail($studentId);
            if ($user->isInstructor()) {
                $instructor = $user->instructor;
                abort_if($student->instructor_id !== $instructor->id, 403, 'Este aluno não é seu.');
            }

            return $student;
        }

        $student = Student::where('user_id', $user->id)->first();
        abort_if(!$student, 403, 'Aluno não encontrado.');

        return $student;
    }

    // ── INDEX (página de treinos do aluno) ────────────────────────────────────
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user    = Auth::user();
        $student = Student::where('user_id', $user->id)->firstOrFail();

        $allWorkouts = Workout::with('workoutExercises')
            ->where('student_id', $student->id)
            ->latest()
            ->get();

        $workoutId = $request->query('workout_id');
        $workout   = $workoutId
            ? Workout::where('student_id', $student->id)->where('id', $workoutId)->first()
            : $allWorkouts->first();

        $exercises = $workout
            ? WorkoutExercise::with('exercise')->where('workout_id', $workout->id)->get()
            : collect();

        return view('workouts.index', compact('workout', 'exercises', 'allWorkouts'));
    }

    public function create(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!($user->isInstructor() || $user->isManager())) {
            return $this->studentWorkoutNotice();
        }

        $exercises = Exercise::all();
        $studentId = $request->query('student_id');
        $student   = $this->resolveStudent($studentId ? (int) $studentId : null);

        return view('workouts.create', compact('exercises', 'student'));
    }

    public function show($id)
    {
        return redirect()->route('workouts.index', ['workout_id' => $id]);
    }

    public function store(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!($user->isInstructor() || $user->isManager())) {
            return $this->studentWorkoutNotice();
        }

        $request->validate([
            'name'        => ['required', 'min:3', 'regex:/^[A-Za-z0-9\s]+$/'],
            'exercise_id' => ['required', 'array'],
            'sets.*'      => ['nullable', 'integer', 'min:1'],
            'reps.*'      => ['nullable', 'integer', 'min:1'],
            'rest_time.*' => ['nullable', 'integer', 'min:1'],
        ], [
            'name.required'        => 'O nome do treino é obrigatório',
            'name.min'             => 'O nome deve ter pelo menos 3 caracteres',
            'name.regex'           => 'Use apenas letras e números',
            'exercise_id.required' => 'Selecione pelo menos um exercício',
        ]);

        $studentId = $request->input('student_id');
        $student   = $this->resolveStudent($studentId ? (int) $studentId : null);

        $workout = Workout::create([
            'student_id' => $student->id,
            'name'       => $request->name,
        ]);

        $validExercise = false;

        foreach ($request->exercise_id as $exerciseId) {
            $sets = $request->sets[$exerciseId] ?? null;
            $reps = $request->reps[$exerciseId] ?? null;

            if (!isset($sets) || !isset($reps) || $sets <= 0 || $reps <= 0) {
                continue;
            }

            $validExercise = true;

            WorkoutExercise::create([
                'workout_id'  => $workout->id,
                'exercise_id' => $exerciseId,
                'sets'        => $sets,
                'reps'        => $reps,
                'rest_time'   => $request->rest_time[$exerciseId] ?? null,
            ]);
        }

        if (!$validExercise) {
            $workout->delete();
            return back()->with('error', 'Preencha séries e reps de pelo menos um exercício')->withInput();
        }

        if ($user->isInstructor() || $user->isManager()) {
            return redirect()->route('dashboard');
        }

        return redirect()->route('workouts.index')->with('success', 'Treino criado com sucesso!');
    }

    public function edit(Request $request, $id)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!($user->isInstructor() || $user->isManager())) {
            return $this->studentWorkoutNotice();
        }

        $workout   = Workout::with('workoutExercises')->findOrFail($id);
        $exercises = Exercise::all();
        $studentId = $request->query('student_id');
        $student   = $this->resolveStudent($studentId ? (int) $studentId : null);

        abort_if($workout->student_id !== $student->id, 403, 'Este treino não pertence a este aluno.');

        return view('workouts.edit', compact('workout', 'exercises', 'student'));
    }

    public function update(Request $request, $id)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!($user->isInstructor() || $user->isManager())) {
            return $this->studentWorkoutNotice();
        }

        $request->validate([
            'name'        => ['required', 'min:3', 'regex:/^[A-Za-z0-9\s]+$/'],
            'exercise_id' => ['required', 'array'],
            'sets.*'      => ['nullable', 'integer', 'min:1'],
            'reps.*'      => ['nullable', 'integer', 'min:1'],
            'rest_time.*' => ['nullable', 'integer', 'min:1'],
        ], [
            'name.required'        => 'O nome do treino é obrigatório',
            'name.min'             => 'O nome deve ter pelo menos 3 caracteres',
            'name.regex'           => 'Use apenas letras e números',
            'exercise_id.required' => 'Selecione pelo menos um exercício',
        ]);

        $studentId = $request->input('student_id');
        $student   = $this->resolveStudent($studentId ? (int) $studentId : null);

        $workout = Workout::findOrFail($id);
        abort_if($workout->student_id !== $student->id, 403, 'Este treino não pertence a este aluno.');

        $workout->update(['name' => $request->name]);

        WorkoutExercise::where('workout_id', $workout->id)->delete();

        $validExercise = false;

        foreach ($request->exercise_id as $exerciseId) {
            $sets = $request->sets[$exerciseId] ?? null;
            $reps = $request->reps[$exerciseId] ?? null;

            if (!isset($sets) || !isset($reps) || $sets <= 0 || $reps <= 0) {
                continue;
            }

            $validExercise = true;

            WorkoutExercise::create([
                'workout_id'  => $workout->id,
                'exercise_id' => $exerciseId,
                'sets'        => $sets,
                'reps'        => $reps,
                'rest_time'   => $request->rest_time[$exerciseId] ?? null,
            ]);
        }

        if (!$validExercise) {
            return back()->with('error', 'Preencha séries e reps de pelo menos um exercício')->withInput();
        }

        if ($user->isInstructor() || $user->isManager()) {
            return redirect()->route('dashboard');
        }

        return redirect()->route('workouts.index')->with('success', 'Treino atualizado!');
    }

    public function destroy(Request $request, $id)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!($user->isInstructor() || $user->isManager())) {
            return $this->studentWorkoutNotice();
        }

        $workout   = Workout::findOrFail($id);
        $studentId = $request->input('student_id');
        $student   = $this->resolveStudent($studentId ? (int) $studentId : null);

        abort_if($workout->student_id !== $student->id, 403, 'Este treino não pertence a este aluno.');

        WorkoutExercise::where('workout_id', $id)->delete();
        $workout->delete();

        if ($user->isInstructor() || $user->isManager()) {
            return redirect()->route('dashboard');
        }

        return redirect()->route('workouts.index')->with('success', 'Treino deletado!');
    }
}
