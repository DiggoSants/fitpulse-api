<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;
use App\Models\Instructor;
use App\Models\Plan;
use App\Models\Enrollment;
use App\Models\Receptionist;
use Carbon\Carbon;

class ReceptionController extends Controller
{
    public function pendingEnrollment()
    {
        return view('reception.index');
    }

    public function pendingEnrollmentData()
    {
        $students = Student::with('user')
            ->whereHas('user', function ($q) {
                $q->whereDoesntHave('instructor')
                  ->whereDoesntHave('manager')
                  ->whereDoesntHave('receptionist');
            })
            ->whereDoesntHave('enrollments', function ($q) {
                $q->where('status', 'active')
                  ->where('end_date', '>=', now()->toDateString());
            })
            ->get()
            ->map(function ($student) {
                return [
                    'id'     => $student->id,
                    'name'   => $student->user->name,
                    'email'  => $student->user->email,
                    'status' => $student->status,
                ];
            })
            ->values();

        return response()->json([
            'data'  => $students,
            'total' => $students->count(),
        ]);
    }

    public function activePlans()
    {
        $plans = Plan::active()
            ->orderBy('name')
            ->get(['id', 'name', 'price', 'duration_days']);

        return response()->json(['data' => $plans]);
    }

    public function availableInstructors()
    {
        $instructors = Instructor::with('user')
            ->get()
            ->map(function ($instructor) {
                return [
                    'id'          => $instructor->id,
                    'name'        => $instructor->user->name,
                    'specialty'   => $instructor->specialty ?? '—',
                    'invite_code' => $instructor->invite_code,
                    'students'    => $instructor->students()->count(),
                ];
            });

        return response()->json(['data' => $instructors]);
    }

    public function enroll(Request $request)
    {
        $request->validate([
            'student_id'    => ['required', 'exists:students,id'],
            'plan_id'       => ['required', 'exists:plans,id'],
            'instructor_id' => ['required', 'exists:instructors,id'],
        ], [
            'student_id.required'    => 'Selecione o aluno',
            'plan_id.required'       => 'Selecione o plano',
            'instructor_id.required' => 'Selecione o instrutor',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $student    = Student::findOrFail($request->student_id);
        $plan       = Plan::where('id', $request->plan_id)->where('status', 'active')->firstOrFail();
        $instructor = Instructor::findOrFail($request->instructor_id);

        if (!$student->user || !$student->user->isStudent()) {
            return response()->json([
                'message' => 'Apenas alunos podem ser matriculados.',
            ], 422);
        }

        if ($student->isEnrolled()) {
            return response()->json([
                'message' => 'Este aluno já possui uma matrícula ativa.',
            ], 422);
        }

        $receptionist = $user->isReceptionist()
            ? Receptionist::where('user_id', $user->id)->first()
            : null;

        $startDate = Carbon::today();
        $endDate   = $startDate->copy()->addDays($plan->duration_days);

        $enrollment = Enrollment::create([
            'student_id'      => $student->id,
            'plan_id'         => $plan->id,
            'receptionist_id' => $receptionist?->id,
            'start_date'      => $startDate,
            'end_date'        => $endDate,
            'status'          => 'active',
        ]);

        $student->update(['instructor_id' => $instructor->id]);

        return response()->json([
            'message' => 'Matrícula realizada com sucesso!',
            'data'    => [
                'enrollment_id'  => $enrollment->id,
                'student'        => $student->user->name,
                'plan'           => $plan->name,
                'instructor'     => $instructor->user->name,
                'start_date'     => $enrollment->start_date->format('d/m/Y'),
                'end_date'       => $enrollment->end_date->format('d/m/Y'),
                'receptionist'   => $receptionist?->user->name ?? 'Gerente',
            ],
        ], 201);
    }
}
