<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Student;
use App\Models\Instructor;
use App\Models\Workout;
use App\Models\WorkoutExercise;

class DashboardController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if ($user->isManager()) {
            $instructors = Instructor::with([
                'user',
                'students.user',
                'students.workouts.workoutExercises.exercise',
            ])->get();

            return view('instructors.dashboard', compact('instructors'));
        }
        if ($user->isInstructor()) {
            $instructor = Instructor::with([
                'user',
                'students.user',
                'students.workouts.workoutExercises.exercise',
            ])->where('user_id', $user->id)->firstOrFail();

            return view('instructors.dashboard', compact('instructor'));
        }
        $student = Student::where('user_id', $user->id)->first();

        if (!$student) {
            return view('dashboard', ['exercises' => collect()]);
        }

        $workout = Workout::where('student_id', $student->id)
            ->latest()
            ->first();

        if (!$workout) {
            return view('dashboard', ['exercises' => collect()]);
        }

        $exercises = WorkoutExercise::with('exercise')
            ->where('workout_id', $workout->id)
            ->get();

        return view('dashboard', compact('exercises', 'workout'));
    }
}
