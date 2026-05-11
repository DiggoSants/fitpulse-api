<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Student;
use App\Models\Instructor;
use App\Models\Receptionist;
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
            $students = Student::with([
                'user',
                'instructor.user',
                'enrollments.plan',
            ])->get();

            $studentsData = $students->map(function ($student) {
                $activeEnrollment = $student->activeEnrollment();

                if (!$activeEnrollment) {
                    $status = 'sem_matricula';
                } elseif ($student->is_defaulter) {
                    $status = 'inadimplente';
                } else {
                    $status = 'ativo';
                }

                return [
                    'id'          => $student->id,
                    'name'        => $student->user->name,
                    'email'       => $student->user->email,
                    'role'        => 'student',
                    'status'      => $status,
                    'instructor'  => $student->instructor
                        ? $student->instructor->user->name
                        : null,
                    'plan'        => $activeEnrollment
                        ? $activeEnrollment->plan->name
                        : null,
                    'plan_end'    => $activeEnrollment
                        ? $activeEnrollment->end_date->format('d/m/Y')
                        : null,
                ];
            });

            $receptionists = Receptionist::with('user')->get()->map(function ($r) {
                return [
                    'id'    => $r->id,
                    'name'  => $r->user->name,
                    'email' => $r->user->email,
                    'role'  => 'receptionist',
                ];
            });

            $instructors = Instructor::with([
                'user',
                'students.user',
                'students.workouts.workoutExercises.exercise',
            ])->get();

            return view('dashboard', compact('studentsData', 'instructors', 'receptionists'));
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

        // ── RECEPCIONISTA ─────────────────────────────────────────────────────
        if ($user->isReceptionist()) {
            return redirect()->route('reception.pending');
        }

        // ── ALUNO ─────────────────────────────────────────────────────────────
        $student = Student::where('user_id', $user->id)->first();

        if (!$student || !$student->isEnrolled()) {
            return view('dashboard', ['enrolled' => false]);
        }

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