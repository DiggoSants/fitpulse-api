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
    /**
     * GET /students/pending-enrollment
     */
    public function pendingEnrollment()
    {
        $students = Student::with('user')
            ->whereHas('user', function ($q) {
                $q->whereDoesntHave('manager')
                  ->whereDoesntHave('instructor')
                  ->whereDoesntHave('receptionist');
            })
            ->get()
            ->filter(fn($s) => !$s->isEnrolled())
            ->map(fn($s) => [
                'id'     => $s->id,
                'name'   => $s->user->name,
                'email'  => $s->user->email,
                'status' => $s->status,
            ])
            ->values();

        return response()->json(['data' => $students, 'total' => $students->count()]);
    }

    /**
     * GET /instructors/available
     */
    public function availableInstructors()
    {
        $instructors = Instructor::with('user')
            ->get()
            ->map(fn($i) => [
                'id'          => $i->id,
                'name'        => $i->user->name,
                'specialty'   => $i->specialty ?? '—',
                'invite_code' => $i->invite_code,
                'students'    => $i->students()->count(),
            ]);

        return response()->json(['data' => $instructors]);
    }

    /**
     * GET /reception/plans
     * Planos ativos — acessível por recepcionistas e gerentes.
     */
    public function activePlans()
    {
        $plans = Plan::where('status', 'active')
            ->get()
            ->map(fn($p) => [
                'id'            => $p->id,
                'name'          => $p->name,
                'price'         => $p->price,
                'duration_days' => $p->duration_days,
                'status'        => $p->status,
            ]);

        return response()->json(['data' => $plans]);
    }

    /**
     * POST /enrollments
     */
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

        $user = Auth::user();

        $student    = Student::findOrFail($request->student_id);
        $plan       = Plan::where('id', $request->plan_id)->where('status', 'active')->firstOrFail();
        $instructor = Instructor::findOrFail($request->instructor_id);

        if ($student->isEnrolled()) {
            return response()->json(['message' => 'Este aluno já possui uma matrícula ativa.'], 422);
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
                'enrollment_id' => $enrollment->id,
                'student'       => $student->user->name,
                'plan'          => $plan->name,
                'instructor'    => $instructor->user->name,
                'start_date'    => $enrollment->start_date->format('d/m/Y'),
                'end_date'      => $enrollment->end_date->format('d/m/Y'),
                'receptionist'  => $receptionist?->user->name ?? 'Gerente',
            ],
        ], 201);
    }
}