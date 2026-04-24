<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Student;
use App\Models\Instructor;
use App\Models\Workout;
use App\Models\WorkoutExercise;
use App\Models\Plan;
use App\Models\Frequency;

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
            ])->whereHas('user', function ($q) {
                $q->whereDoesntHave('manager')
                  ->whereDoesntHave('instructor');
            })->get();

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
                    'id'         => $student->id,
                    'name'       => $student->user->name,
                    'email'      => $student->user->email,
                    'status'     => $status,
                    'instructor' => $student->instructor
                        ? $student->instructor->user->name
                        : null,
                    'plan'       => $activeEnrollment
                        ? $activeEnrollment->plan->name
                        : null,
                    'plan_end'   => $activeEnrollment
                        ? $activeEnrollment->end_date->format('d/m/Y')
                        : null,
                ];
            });

            $instructors = Instructor::with([
                'user',
                'students.user',
                'students.workouts.workoutExercises.exercise',
            ])->get();

            $plans = Plan::orderBy('status')->orderBy('name')->get();

            $totalStudents             = $students->count();
            $activeStudents            = $studentsData->where('status', 'ativo')->count();
            $defaulterStudents         = $studentsData->where('status', 'inadimplente')->count();
            $studentsWithoutEnrollment = $studentsData->where('status', 'sem_matricula')->count();
            $totalInstructors          = $instructors->count();
            $totalPlans                = $plans->count();

            return view('dashboard', compact(
                'studentsData',
                'instructors',
                'totalStudents',
                'activeStudents',
                'defaulterStudents',
                'studentsWithoutEnrollment',
                'totalInstructors',
                'plans',
                'totalPlans'
            ));
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

        if (!$student || !$student->isEnrolled()) {
            return view('dashboard', ['enrolled' => false]);
        }

        $workout = Workout::where('student_id', $student->id)
            ->latest()
            ->first();

        // Frequência do mês atual
        $frequencyThisMonth = Frequency::where('student_id', $student->id)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // Última presença
        $lastFrequency = Frequency::where('student_id', $student->id)
            ->latest()
            ->first();

        // Já registrou hoje?
        $checkedInToday = Frequency::where('student_id', $student->id)
            ->whereDate('created_at', today())
            ->exists();

        // ── NOVO: dias da semana com presença ────────────────────────────────
        $startOfWeek = now()->startOfWeek(\Carbon\Carbon::SUNDAY);
        $endOfWeek   = now()->endOfWeek(\Carbon\Carbon::SATURDAY);

        $frequencyThisWeek = Frequency::where('student_id', $student->id)
            ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
            ->get()
            ->map(fn($f) => $f->created_at->dayOfWeek) // 0=Dom ... 6=Sab
            ->unique()
            ->values()
            ->toArray();
        // ─────────────────────────────────────────────────────────────────────

        if (!$workout) {
            return view('dashboard', compact(
                'frequencyThisMonth',
                'lastFrequency',
                'checkedInToday',
                'frequencyThisWeek'
            ) + ['enrolled' => true, 'exercises' => collect()]);
        }

        $exercises = WorkoutExercise::with('exercise')
            ->where('workout_id', $workout->id)
            ->get();

        return view('dashboard', compact(
            'exercises',
            'workout',
            'frequencyThisMonth',
            'lastFrequency',
            'checkedInToday',
            'frequencyThisWeek'
        ) + ['enrolled' => true]);
    }
}