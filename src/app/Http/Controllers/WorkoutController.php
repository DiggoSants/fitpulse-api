<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Workout;
use App\Models\Exercise;
use App\Models\WorkoutExercise;
use App\Models\Student;

class WorkoutController extends Controller
{

    public function create()
    {
        $students = Student::all();
        $exercises = Exercise::all();

        return view('workouts.create', compact('students', 'exercises'));
    }

    public function store(Request $request)
    {
        $workout = Workout::create([
            'student_id' => $request->student_id,
            'name' => $request->name
        ]);

        if ($request->exercise_id) {
            foreach ($request->exercise_id as $key => $exercise) {

                if (
                    !isset($request->sets[$key]) ||
                    !isset($request->reps[$key]) ||
                    empty($request->sets[$key]) ||
                    empty($request->reps[$key])
                ) {
                    continue;
                }

                WorkoutExercise::create([
                    'workout_id' => $workout->id,
                    'exercise_id' => $exercise,
                    'sets' => $request->sets[$key],
                    'reps' => $request->reps[$key],
                    'rest_time' => $request->rest_time[$key] ?? null
                ]);
            }
        }

        return redirect('/dashboard');
    }

    public function edit($id)
    {
        $workout = Workout::with('workoutExercises')->findOrFail($id);
        $students = Student::all();
        $exercises = Exercise::all();

        return view('workouts.edit', compact('workout','students','exercises'));
    }

    public function update(Request $request, $id)
    {
        $workout = Workout::findOrFail($id);

        $workout->update([
            'student_id' => $request->student_id,
            'name' => $request->name
        ]);

        WorkoutExercise::where('workout_id', $workout->id)->delete();

        if ($request->exercise_id) {
            foreach ($request->exercise_id as $key => $exercise) {

                if (empty($request->sets[$key]) || empty($request->reps[$key])) {
                    continue;
                }

                WorkoutExercise::create([
                    'workout_id' => $workout->id,
                    'exercise_id' => $exercise,
                    'sets' => $request->sets[$key],
                    'reps' => $request->reps[$key],
                    'rest_time' => $request->rest_time[$key] ?? null
                ]);
            }
        }

        return redirect('/dashboard');
    }
}