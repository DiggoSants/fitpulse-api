<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Plan;
use App\Models\Student;
use App\Models\Enrollment;
use Carbon\Carbon;

class EnrollmentController extends Controller
{
    /**
     * Tela de escolha de plano.
     * Só acessível após login — redireciona para dashboard se já tiver matrícula ativa.
     */
    public function index()
    {
        /** @var \App\Models\User $user */
        $user    = Auth::user();
        $student = Student::where('user_id', $user->id)->first();

        // Se já tem matrícula ativa, manda para o dashboard
        if ($student && $student->isEnrolled()) {
            return redirect()->route('dashboard');
        }

        $plans = Plan::all();

        return view('enrollments.index', compact('plans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'plan_id' => ['required', 'exists:plans,id'],
        ], [
            'plan_id.required' => 'Selecione um plano',
            'plan_id.exists'   => 'Plano inválido',
        ]);

        /** @var \App\Models\User $user */
        $user    = Auth::user();
        $student = Student::where('user_id', $user->id)->firstOrFail();

        $plan      = Plan::findOrFail($request->plan_id);
        $startDate = Carbon::today();
        $endDate   = $startDate->copy()->addDays($plan->duration_days);

        Enrollment::create([
            'student_id' => $student->id,
            'plan_id'    => $plan->id,
            'start_date' => $startDate,
            'end_date'   => $endDate,
        ]);

        return redirect()->route('dashboard')->with('success', 'Matrícula realizada com sucesso!');
    }
}