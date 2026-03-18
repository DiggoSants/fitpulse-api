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

                WorkoutExercise::create([
                    'workout_id' => $workout->id,
                    'exercise_id' => $exercise,
                    'sets' => $request->sets[$key],
                    'reps' => $request->reps[$key],
                    'rest_time' => $request->rest_time[$key]
                ]);
            }
        }

        return redirect('/dashboard');
    }
}
