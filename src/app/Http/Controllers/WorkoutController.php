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

    public function create()
    {
        $exercises = Exercise::all();

        return view('workouts.create', compact('exercises'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'min:3', 'regex:/^[A-Za-z0-9\s]+$/'],

            'exercise_id' => ['required', 'array'],
            'sets.*' => ['nullable', 'integer', 'min:1'],
            'reps.*' => ['nullable', 'integer', 'min:1'],
            'rest_time.*' => ['nullable', 'integer', 'min:1'],

        ], [
            'name.required' => 'O nome do treino é obrigatório',
            'name.min' => 'O nome deve ter pelo menos 3 caracteres',
            'name.regex' => 'Use apenas letras e números',

            'exercise_id.required' => 'Selecione pelo menos um exercício',
            'sets.*.min' => 'Séries devem ser no mínimo 1',
            'reps.*.min' => 'Reps devem ser no mínimo 1',
        ]);
        if (!$request->exercise_id || count($request->exercise_id) === 0) {
            return back()
                ->with('error', 'Selecione pelo menos um exercício')
                ->withInput();
        }

        // pega aluno logado
        $student = Student::where('user_id', Auth::id())->first();
        if (!$student) {
            return back()->with('error', 'Aluno não encontrado');
        }
        $workout = Workout::create([
            'student_id' => $student->id,
            'name' => $request->name
        ]);

        $validExercise = false;

        foreach ($request->exercise_id as $exerciseId) {

            $sets = $request->sets[$exerciseId] ?? null;
            $reps = $request->reps[$exerciseId] ?? null;

            if (
                !isset($sets) || !isset($reps) ||
                $sets <= 0 || $reps <= 0
            ) {
                continue;
            }

            $validExercise = true;

            WorkoutExercise::create([
                'workout_id' => $workout->id,
                'exercise_id' => $exerciseId,
                'sets' => $sets,
                'reps' => $reps,
                'rest_time' => $request->rest_time[$exerciseId] ?? null
            ]);
        }

        if (!$validExercise) {
            $workout->delete();

            return back()
                ->with('error', 'Preencha séries e reps de pelo menos um exercício')
                ->withInput();
        }

        return redirect('/dashboard');
    }

    public function edit($id)
    {
        $workout = Workout::with('workoutExercises')->findOrFail($id);
        $exercises = Exercise::all();

        return view('workouts.edit', compact('workout', 'exercises'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => ['required', 'min:3', 'regex:/^[A-Za-z0-9\s]+$/'],

            'exercise_id' => ['required', 'array'],
            'sets.*' => ['nullable', 'integer', 'min:1'],
            'reps.*' => ['nullable', 'integer', 'min:1'],
            'rest_time.*' => ['nullable', 'integer', 'min:1'],

        ], [
            'name.required' => 'O nome do treino é obrigatório',
            'name.min' => 'O nome deve ter pelo menos 3 caracteres',
            'name.regex' => 'Use apenas letras e números',

            'exercise_id.required' => 'Selecione pelo menos um exercício',
            'sets.*.min' => 'Séries devem ser no mínimo 1',
            'reps.*.min' => 'Reps devem ser no mínimo 1',
        ]);

        if (!$request->exercise_id || count($request->exercise_id) === 0) {
            return back()
                ->with('error', 'Selecione pelo menos um exercício')
                ->withInput();
        }

        $workout = Workout::findOrFail($id);

        $workout->update([
            'name' => $request->name
        ]);

        // apaga antigos
        WorkoutExercise::where('workout_id', $workout->id)->delete();

        $validExercise = false;

        foreach ($request->exercise_id as $exerciseId) {

            $sets = $request->sets[$exerciseId] ?? null;
            $reps = $request->reps[$exerciseId] ?? null;

            if (
                !isset($sets) || !isset($reps) ||
                $sets <= 0 || $reps <= 0
            ) {
                continue;
            }

            $validExercise = true;

            WorkoutExercise::create([
                'workout_id' => $workout->id,
                'exercise_id' => $exerciseId,
                'sets' => $sets,
                'reps' => $reps,
                'rest_time' => $request->rest_time[$exerciseId] ?? null
            ]);
        }

        if (!$validExercise) {
            return back()
                ->with('error', 'Preencha séries e reps de pelo menos um exercício')
                ->withInput();
        }

        return redirect('/dashboard');
    }

    public function destroy($id)
    {
        $workout = Workout::findOrFail($id);

        WorkoutExercise::where('workout_id', $id)->delete();

        $workout->delete();

        return redirect('/dashboard');
    }
}
