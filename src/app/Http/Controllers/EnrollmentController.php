<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Plan;
use App\Models\Student;
use App\Models\Instructor;
use App\Models\Enrollment;
use Carbon\Carbon;

class EnrollmentController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user    = Auth::user();
        $student = Student::where('user_id', $user->id)->first();
        if ($student && $student->isEnrolled()) {
            return redirect()->route('dashboard');
        }

        $plans = Plan::all();

        return view('enrollments.index', compact('plans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'plan_id'     => ['required', 'exists:plans,id'],
            'invite_code' => ['required', 'string'],
        ], [
            'plan_id.required'     => 'Selecione um plano',
            'plan_id.exists'       => 'Plano inválido',
            'invite_code.required' => 'Insira o código do seu instrutor',
        ]);

        $instructor = Instructor::where('invite_code', strtoupper($request->invite_code))->first();

        if (!$instructor) {
            return back()
                ->withInput()
                ->withErrors(['invite_code' => 'Código de instrutor inválido.']);
        }

        /** @var \App\Models\User $user */
        $user    = Auth::user();
        $student = Student::where('user_id', $user->id)->firstOrFail();

       
        Enrollment::where('student_id', $student->id)
            ->where('status', 'active')
            ->update([
                'status'       => 'cancelled',
                'cancelled_at' => now(),
            ]);

        $plan      = Plan::findOrFail($request->plan_id);
        $startDate = Carbon::today();
        $endDate   = $startDate->copy()->addDays($plan->duration_days);

        Enrollment::create([
            'student_id' => $student->id,
            'plan_id'    => $plan->id,
            'start_date' => $startDate,
            'end_date'   => $endDate,
        ]);

        $student->update(['instructor_id' => $instructor->id]);

        return redirect()->route('dashboard')->with('success', 'Matrícula realizada com sucesso!');
    }
}