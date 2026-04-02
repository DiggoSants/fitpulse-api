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

        // ── GERENTE ───────────────────────────────────────────────────────────
        if ($user->isManager()) {
            $instructors = Instructor::with([
                'user',
                'students.user',
                'students.workouts.workoutExercises.exercise',
            ])->get();

            return view('dashboard', compact('instructors'));
        }

        // ── INSTRUTOR ─────────────────────────────────────────────────────────
        if ($user->isInstructor()) {
            $instructor = Instructor::with([
                'user',
                'students.user',
                'students.workouts.workoutExercises.exercise',
            ])->where('user_id', $user->id)->firstOrFail();

            return view('dashboard', compact('instructor'));
        }

        // ── ALUNO ─────────────────────────────────────────────────────────────
        $student = Student::where('user_id', $user->id)->first();

        // Aluno sem matrícula ativa — dashboard limitado
        if (!$student || !$student->isEnrolled()) {
            return view('dashboard', ['enrolled' => false]);
        }

        // Aluno com matrícula ativa — dashboard completo
        $workout = Workout::where('student_id', $student->id)
            ->latest()
            ->first();

        if (!$workout) {
            return view('dashboard', ['enrolled' => true, 'exercises' => collect()]);
        }

        $exercises = WorkoutExercise::with('exercise')
            ->where('workout_id', $workout->id)
            ->get();

        return view('dashboard', compact('exercises', 'workout') + ['enrolled' => true]);
    }
}