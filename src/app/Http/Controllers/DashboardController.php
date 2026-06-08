<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Student;
use App\Models\Instructor;
use App\Models\Receptionist;
use App\Models\Workout;
use App\Models\WorkoutExercise;
use App\Models\Frequency;
use App\Models\Plan;

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
            ])
                ->whereHas('user', function ($query) {
                    $query->whereDoesntHave('instructor')
                        ->whereDoesntHave('manager')
                        ->whereDoesntHave('receptionist');
                })
                ->get();

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

            // Ordena: devendo → ativo → sem matrícula → bloqueado, depois A→Z dentro de cada grupo
            $statusOrder = ['inadimplente' => 0, 'ativo' => 1, 'sem_matricula' => 2, 'bloqueado' => 3];
            $studentsData = $studentsData->sortBy([
                fn ($a, $b) => ($statusOrder[$a['status']] ?? 99) <=> ($statusOrder[$b['status']] ?? 99),
                fn ($a, $b) => mb_strtolower($a['name']) <=> mb_strtolower($b['name']),
            ])->values();

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

            $plans = Plan::orderedByPrice()->get();

            return view('dashboard', [
                'studentsData'     => $studentsData,
                'instructors'      => $instructors,
                'receptionists'    => $receptionists,
                'plans'            => $plans,
                'totalStudents'    => $studentsData->count(),
                'activeStudents'   => $studentsData->where('status', 'ativo')->count(),
                'totalInstructors' => $instructors->count(),
                'totalPlans'       => $plans->count(),
            ]);
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
            return view('reception.index');
        }

        // ── ALUNO ─────────────────────────────────────────────────────────────
        $student = Student::where('user_id', $user->id)->first();

        if (!$student || !$student->isEnrolled()) {
            return view('dashboard', ['enrolled' => false]);
        }

        $activeEnrollment = $student->activeEnrollment();
        $checkedInToday = Frequency::where('student_id', $student->id)
            ->where(function ($query) use ($activeEnrollment) {
                $query->where('enrollment_id', $activeEnrollment->id)
                    ->orWhere(function ($legacyQuery) use ($activeEnrollment) {
                        $legacyQuery->whereNull('enrollment_id')
                            ->where('created_at', '>=', $activeEnrollment->created_at);
                    });
            })
            ->whereDate('created_at', today())
            ->exists();
        $lastFrequency = Frequency::where('student_id', $student->id)
            ->where(function ($query) use ($activeEnrollment) {
                $query->where('enrollment_id', $activeEnrollment->id)
                    ->orWhere(function ($legacyQuery) use ($activeEnrollment) {
                        $legacyQuery->whereNull('enrollment_id')
                            ->where('created_at', '>=', $activeEnrollment->created_at);
                    });
            })
            ->latest()
            ->first();
        $frequencyThisWeek = Frequency::where('student_id', $student->id)
            ->where(function ($query) use ($activeEnrollment) {
                $query->where('enrollment_id', $activeEnrollment->id)
                    ->orWhere(function ($legacyQuery) use ($activeEnrollment) {
                        $legacyQuery->whereNull('enrollment_id')
                            ->where('created_at', '>=', $activeEnrollment->created_at);
                    });
            })
            ->whereBetween('created_at', [
                now()->startOfWeek(\Carbon\Carbon::SUNDAY),
                now()->endOfWeek(\Carbon\Carbon::SATURDAY),
            ])
            ->get()
            ->map(fn ($frequency) => $frequency->created_at->dayOfWeek)
            ->unique()
            ->values()
            ->all();

        $workout = Workout::where('student_id', $student->id)
            ->latest()
            ->first();

        if (!$workout) {
            return view('dashboard', [
                'enrolled'         => true,
                'exercises'        => collect(),
                'activeEnrollment' => $activeEnrollment,
                'checkedInToday'   => $checkedInToday,
                'lastFrequency'    => $lastFrequency,
                'frequencyThisWeek'=> $frequencyThisWeek,
            ]);
        }

        $exercises = WorkoutExercise::with('exercise')
            ->where('workout_id', $workout->id)
            ->get();

        return view('dashboard', compact(
            'exercises',
            'workout',
            'activeEnrollment',
            'checkedInToday',
            'lastFrequency',
            'frequencyThisWeek'
        ) + ['enrolled' => true]);
    }
}