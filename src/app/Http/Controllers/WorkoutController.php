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
        //pega aluno logado
        $student = Student::where('user_id', Auth::id())->first();

        $workout = Workout::create([
            'student_id' => $student->id,
            'name' => $request->name
        ]);

        if ($request->exercise_id) {
            foreach ($request->exercise_id as $exerciseId) {

                $sets = $request->sets[$exerciseId] ?? null;
                $reps = $request->reps[$exerciseId] ?? null;

                if (!$sets || !$reps) {
                    continue;
                }

                WorkoutExercise::create([
                    'workout_id' => $workout->id,
                    'exercise_id' => $exerciseId,
                    'sets' => $sets,
                    'reps' => $reps,
                    'rest_time' => $request->rest_time[$exerciseId] ?? null
                ]);
            }
        }

        return redirect('/dashboard');
    }

    public function edit($id)
    {
        $workout = Workout::with('workoutExercises')->findOrFail($id);
        $exercises = Exercise::all();

        return view('workouts.edit', compact('workout','exercises'));
    }

    public function update(Request $request, $id)
    {
        $workout = Workout::findOrFail($id);

        //mantém o mesmo aluno (não deixa trocar)
        $workout->update([
            'name' => $request->name
        ]);

        WorkoutExercise::where('workout_id', $workout->id)->delete();

        if ($request->exercise_id) {
            foreach ($request->exercise_id as $exerciseId) {

                $sets = $request->sets[$exerciseId] ?? null;
                $reps = $request->reps[$exerciseId] ?? null;

                if (!$sets || !$reps) {
                    continue;
                }

                WorkoutExercise::create([
                    'workout_id' => $workout->id,
                    'exercise_id' => $exerciseId,
                    'sets' => $sets,
                    'reps' => $reps,
                    'rest_time' => $request->rest_time[$exerciseId] ?? null
                ]);
            }
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