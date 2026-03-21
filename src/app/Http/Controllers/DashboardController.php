<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;
use App\Models\WorkoutExercise;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $student = Student::where('user_id', $user->id)->first();

        if (!$student) {
            return view('dashboard', ['exercises' => []]);
        }

        $workout = \App\Models\Workout::where('student_id', $student->id)
            ->latest()
            ->first();

        if (!$workout) {
            return view('dashboard', ['exercises' => []]);
        }

        $exercises = \App\Models\WorkoutExercise::with('exercise')
            ->where('workout_id', $workout->id)
            ->get();

        return view('dashboard', compact('exercises', 'workout'));
    }
}
