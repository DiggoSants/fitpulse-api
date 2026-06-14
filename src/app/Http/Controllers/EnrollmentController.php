<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Plan;
use App\Models\Student;
use App\Models\Instructor;
use App\Models\Enrollment;
use App\Models\StudentSchedule;
use App\Models\InstructorAvailability;
use App\Services\BillingService;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

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

        $plans = Plan::active()->orderedByPrice()->get();
        $weekDays = StudentSchedule::weekDays();
        $shiftLabels = StudentSchedule::shiftLabels();
        $selectedSchedule = $student
            ? StudentSchedule::where('user_id', $user->id)
                ->where('active', true)
                ->get(['week_day', 'shift'])
            : collect();
        $selectedScheduleDays = $selectedSchedule->pluck('week_day')->toArray();
        $selectedScheduleShifts = $selectedSchedule
            ->pluck('shift', 'week_day')
            ->map(fn ($shift) => $shift ?: 'full_day')
            ->toArray();
        $occupiedSlotsByInstructor = $this->occupiedSlotsByInstructor($student?->id);
        $instructorOptions = Instructor::with([
                'user:id,name',
                'availability' => fn ($query) => $query->where('active', true),
            ])
            ->withCount('students')
            ->get()
            ->map(function ($instructor) use ($occupiedSlotsByInstructor) {
                return [
                    'id' => $instructor->id,
                    'name' => $instructor->user?->name ?? 'Instrutor',
                    'specialty' => $instructor->specialty,
                    'students_count' => $instructor->students_count,
                    'availability' => $instructor->availability
                        ->map(function ($availability) use ($occupiedSlotsByInstructor, $instructor) {
                            $occupiedShifts = $occupiedSlotsByInstructor[$instructor->id][$availability->week_day] ?? [];

                            return [
                                'week_day' => $availability->week_day,
                                'shift' => $availability->shift,
                                'shift_label' => InstructorAvailability::shiftLabels()[$availability->shift] ?? $availability->shift,
                                'time_label' => $this->availabilityTimeLabel($availability),
                                'occupied' => $this->slotIsOccupied($availability->shift, $occupiedShifts),
                            ];
                        })
                        ->values(),
                ];
            })
            ->values();

        return view('enrollments.index', compact(
            'plans',
            'weekDays',
            'shiftLabels',
            'selectedScheduleDays',
            'selectedScheduleShifts',
            'instructorOptions'
        ));
    }

    /**
     * Matrícula com distribuição automática de instrutor disponível.
     */
    public function store(Request $request, BillingService $billingService)
    {
        $weekDayKeys = array_keys(StudentSchedule::weekDays());
        $shiftKeys = array_keys(StudentSchedule::shiftLabels());

        $request->validate([
            'plan_id'         => ['required', 'exists:plans,id'],
            'payment_method'  => ['required', 'in:credit_card,debit_card,pix'],
            'goal'            => ['required', 'string', 'in:hypertrophy,weight_loss,conditioning,health,rehabilitation,other'],
            'custom_goal'     => ['nullable', 'string', 'required_if:goal,other', 'max:500'],
            'days'            => ['required', 'array', 'min:' . StudentSchedule::MIN_DAYS],
            'days.*'          => ['required', 'string', 'distinct', Rule::in($weekDayKeys)],
            'shifts'          => ['required', 'array'],
            'shifts.*'        => ['required', 'string', Rule::in($shiftKeys)],
        ], [
            'payment_method.required' => 'Informe o método de pagamento',
            'payment_method.in'       => 'Método de pagamento inválido.',
            'plan_id.required'        => 'Selecione um plano',
            'plan_id.exists'          => 'Plano inválido',
            'goal.required'           => 'Selecione seu objetivo',
            'goal.in'                 => 'Objetivo inválido',
            'custom_goal.required_if' => 'Descreva seu objetivo personalizado',
            'custom_goal.max'         => 'O objetivo não pode ter mais de 500 caracteres',
            'days.required'           => 'Selecione os dias que deseja treinar.',
            'days.array'              => 'Agenda de treino inválida.',
            'days.min'                => 'Selecione pelo menos ' . StudentSchedule::MIN_DAYS . ' dias de treino na semana.',
            'days.*.in'               => 'Dia da semana inválido.',
            'days.*.distinct'         => 'Não repita dias na agenda de treino.',
            'shifts.required'         => 'Informe o turno de treino para cada dia escolhido.',
            'shifts.array'            => 'Turnos da agenda inválidos.',
            'shifts.*.required'       => 'Informe o turno de treino para cada dia escolhido.',
            'shifts.*.in'             => 'Turno de treino inválido.',
        ]);

        /** @var \App\Models\User $user */
        $user    = Auth::user();
        $student = Student::where('user_id', $user->id)->firstOrFail();
        
        // Salvar objetivo do aluno
        $updateData = ['goal' => $request->input('goal')];
        
        // Se objetivo for "outro", também salvar a descrição customizada
        if ($request->input('goal') === 'other' && $request->filled('custom_goal')) {
            $updateData['custom_goal'] = $request->input('custom_goal');
        }
        
        $student->update($updateData);

        $studentScheduleDays = array_values(array_unique($request->input('days', [])));
        $studentSchedule = $this->scheduleFromSelection($studentScheduleDays, $request->input('shifts', []));

        if (count($studentSchedule) !== count($studentScheduleDays)) {
            throw ValidationException::withMessages([
                'shifts' => 'Informe o turno de treino para cada dia escolhido.',
            ]);
        }

        // --------------------------------------------------------------
        // 1. Buscar instrutores que cobrem TODOS os dias e turnos livres
        // --------------------------------------------------------------
        $availableInstructors = $this->availableInstructorsForSchedule($studentSchedule, $student->id);

        if ($availableInstructors->isEmpty()) {
            $message = 'Nenhum instrutor disponível para os dias e horários da sua agenda. Entre em contato com a recepção.';
            if ($request->expectsJson()) {
                return response()->json(['message' => $message], 422);
            }
            return back()->withInput()->withErrors(['instructor' => $message]);
        }

        // --------------------------------------------------------------
        // 2. Selecionar instrutor com menos alunos (balanceamento de carga)
        // --------------------------------------------------------------
        $selectedInstructor = $availableInstructors->first();

        // --------------------------------------------------------------
        // 3. Processar plano, agenda e matrícula
        // --------------------------------------------------------------
        $plan      = Plan::where('status', 'active')->findOrFail($request->plan_id);
        $startDate = Carbon::today();
        $endDate   = $startDate->copy()->addDays($plan->duration_days);

        [$billing, $enrollment] = DB::transaction(function () use ($student, $selectedInstructor, $plan, $startDate, $endDate, $request, $billingService, $studentSchedule) {
            $lockedStudent = Student::whereKey($student->id)->lockForUpdate()->firstOrFail();

            if ($lockedStudent->isEnrolled()) {
                throw ValidationException::withMessages([
                    'plan_id' => 'Você já possui uma matrícula ativa.',
                ]);
            }

            StudentSchedule::where('user_id', $lockedStudent->user_id)->delete();

            foreach ($studentSchedule as $scheduleItem) {
                StudentSchedule::create([
                    'user_id'  => $lockedStudent->user_id,
                    'week_day' => $scheduleItem['week_day'],
                    'shift'    => $scheduleItem['shift'],
                    'active'   => true,
                ]);
            }

            $enrollment = Enrollment::create([
                'student_id' => $lockedStudent->id,
                'plan_id'    => $plan->id,
                'start_date' => $startDate,
                'end_date'   => $endDate,
                'status'     => 'active',
            ]);

            $lockedStudent->update([
                'instructor_id' => $selectedInstructor->id,
            ]);

            return [
                $billingService->createForEnrollment($lockedStudent, $enrollment, $request->payment_method),
                $enrollment,
            ];
        });

        $instructorName = $selectedInstructor->user->name;
        $successMessage = "Matrícula realizada! Instrutor: {$instructorName}. " . $billingService->messageForStatus($billing->status);

        if ($request->expectsJson()) {
            return response()->json([
                'message'     => $successMessage,
                'instructor'  => $selectedInstructor->only(['id', 'user.name']),
                'enrollment'  => $enrollment,
            ]);
        }

        return redirect()
            ->route('dashboard')
            ->with('success', $successMessage);
    }

    private function scheduleFromSelection(array $days, array $shifts): array
    {
        $schedule = [];

        foreach ($days as $day) {
            $shift = $shifts[$day] ?? null;

            if (!$shift) {
                continue;
            }

            $schedule[] = [
                'week_day' => $day,
                'shift'    => $shift,
            ];
        }

        return $schedule;
    }

    private function availableInstructorsForSchedule(array $schedule, ?int $ignoredStudentId = null)
    {
        $occupiedSlotsByInstructor = $this->occupiedSlotsByInstructor($ignoredStudentId);

        return Instructor::with([
                'user',
                'availability' => fn ($query) => $query->where('active', true),
            ])
            ->withCount('students')
            ->get()
            ->filter(function (Instructor $instructor) use ($schedule, $occupiedSlotsByInstructor) {
                foreach ($schedule as $slot) {
                    $hasAvailability = $instructor->availability->contains(function (InstructorAvailability $availability) use ($slot) {
                        return $availability->week_day === $slot['week_day']
                            && $availability->shift === $slot['shift']
                            && $availability->active;
                    });

                    if (!$hasAvailability) {
                        return false;
                    }

                    $occupiedShifts = $occupiedSlotsByInstructor[$instructor->id][$slot['week_day']] ?? [];

                    if ($this->slotIsOccupied($slot['shift'], $occupiedShifts)) {
                        return false;
                    }
                }

                return true;
            })
            ->sortBy(fn (Instructor $instructor) => sprintf(
                '%08d|%s',
                (int) ($instructor->students_count ?? 0),
                mb_strtolower($instructor->user?->name ?? '')
            ))
            ->values();
    }

    private function occupiedSlotsByInstructor(?int $ignoredStudentId = null): array
    {
        $rows = StudentSchedule::query()
            ->join('users', 'users.id', '=', 'student_schedules.user_id')
            ->join('students', 'students.user_id', '=', 'users.id')
            ->where('student_schedules.active', true)
            ->whereNotNull('students.instructor_id')
            ->whereNotNull('student_schedules.shift')
            ->when($ignoredStudentId, fn ($query) => $query->where('students.id', '<>', $ignoredStudentId))
            ->get([
                'students.instructor_id',
                'student_schedules.week_day',
                'student_schedules.shift',
            ]);

        $slots = [];

        foreach ($rows as $row) {
            $shift = (string) $row->shift;

            if (!$shift) {
                continue;
            }

            $instructorId = (int) $row->instructor_id;
            $weekDay      = (string) $row->week_day;

            $slots[$instructorId][$weekDay] ??= [];
            $slots[$instructorId][$weekDay][] = $shift;
            $slots[$instructorId][$weekDay] = array_values(array_unique($slots[$instructorId][$weekDay]));
        }

        return $slots;
    }

    private function slotIsOccupied(string $shift, array $occupiedShifts): bool
    {
        foreach ($occupiedShifts as $occupiedShift) {
            if ($this->shiftsConflict($shift, (string) $occupiedShift)) {
                return true;
            }
        }

        return false;
    }

    private function shiftsConflict(string $left, string $right): bool
    {
        // full_day só conflita com outro full_day
        if ($left === 'full_day' && $right === 'full_day') {
            return true;
        }

        if ($left === 'full_day' || $right === 'full_day') {
            return false;
        }

        return $left === $right;
    }

    private function availabilityTimeLabel(InstructorAvailability $availability): string
    {
        if ($availability->start_time && $availability->end_time) {
            return $this->formatAvailabilityTime($availability->start_time) . ' às ' . $this->formatAvailabilityTime($availability->end_time);
        }

        return InstructorAvailability::shiftLabels()[$availability->shift] ?? $availability->shift;
    }

    private function formatAvailabilityTime($time): string
    {
        if ($time instanceof \Carbon\CarbonInterface) {
            return $time->format('H:i');
        }

        return mb_substr((string) $time, 0, 5);
    }

    /**
     * Cancela a matrícula respeitando o período já pago.
     */
    public function cancel(Request $request, $id)
    {
        /** @var \App\Models\User $user */
        $user       = Auth::user();
        $enrollment = Enrollment::with('student')->findOrFail($id);

        // Aluno só pode cancelar a própria matrícula
        if ($user->isStudent()) {
            $student = Student::where('user_id', $user->id)->first();

            if (!$student || $enrollment->student_id !== $student->id) {
                if (!$request->expectsJson()) {
                    return back()->with('error', 'Você não tem permissão para cancelar esta matrícula.');
                }

                return response()->json([
                    'message' => 'Você não tem permissão para cancelar esta matrícula.',
                ], 403);
            }
        } elseif (!$user->isManager()) {
            if (!$request->expectsJson()) {
                return back()->with('error', 'Apenas o próprio aluno ou um gerente podem cancelar matrículas.');
            }

            return response()->json([
                'message' => 'Apenas o próprio aluno ou um gerente podem cancelar matrículas.',
            ], 403);
        }

        $openEnrollments = Enrollment::where('student_id', $enrollment->student_id)
            ->where('status', 'active')
            ->where('end_date', '>=', now()->toDateString())
            ->get();

        // Verifica se já foi cancelada
        if ($enrollment->status === 'cancelled' && $openEnrollments->isEmpty()) {
            if (!$request->expectsJson()) {
                return redirect()->route('dashboard')->with('info', 'Esta matrícula já foi cancelada.');
            }

            return response()->json([
                'message' => 'Esta matrícula já foi cancelada.',
            ], 422);
        }

        // Verifica se a matrícula já expirou
        if ($enrollment->end_date->lt(now()->startOfDay()) && $openEnrollments->isEmpty()) {
            if (!$request->expectsJson()) {
                return back()->with('error', 'Matrícula já expirada. Não é possível cancelar.');
            }

            return response()->json([
                'message' => 'Matrícula já expirada.',
            ], 400);
        }

        // Cancela todas as matrículas pagas ainda abertas, mantendo end_date inalterado.
        DB::transaction(function () use ($openEnrollments) {
            foreach ($openEnrollments as $openEnrollment) {
                $openEnrollment->update([
                    'status'       => 'cancelled',
                    'cancelled_at' => now(),
                ]);
            }
        });

        $accessEnrollment = Enrollment::where('student_id', $enrollment->student_id)
            ->where('status', 'cancelled')
            ->where('end_date', '>=', now()->toDateString())
            ->orderByDesc('end_date')
            ->orderByDesc('id')
            ->first() ?? $enrollment->fresh();

        $daysLeft = $accessEnrollment->daysLeft();
        $endDate  = $accessEnrollment->end_date->format('d/m/Y');

        $message = "Cancelamento solicitado. Você mantém acesso até {$endDate} (faltam {$daysLeft} dias).";

        if (!$request->expectsJson()) {
            return redirect()->route('dashboard')->with('success', $message);
        }

        return response()->json([
            'message'   => $message,
            'days_left' => $daysLeft,
            'end_date'  => $endDate,
        ]);
    }

    /**
     * Realiza matrícula de teste grátis (apenas uma vez por aluno)
     */
    public function trial(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->firstOrFail();

        // Verifica se já usou teste
        if ($student->hasUsedTrial()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Você já utilizou o teste grátis. Contrate um plano para continuar.'], 403);
            }
            return back()->with('error', 'Você já utilizou o teste grátis. Contrate um plano para continuar.');
        }

        // Busca o plano ativo que seja do tipo teste
        $trialPlan = Plan::where('is_trial', true)->where('status', 'active')->first();

        if (!$trialPlan) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Nenhum plano de teste disponível no momento.'], 404);
            }
            return back()->with('error', 'Nenhum plano de teste disponível.');
        }

        // Cria a matrícula com data fim baseada nos trial_days
        $startDate = now();
        $endDate   = $startDate->copy()->addDays($trialPlan->trial_days);

        $enrollment = Enrollment::create([
            'student_id' => $student->id,
            'plan_id'    => $trialPlan->id,
            'start_date' => $startDate,
            'end_date'   => $endDate,
            'status'     => 'active',
        ]);

        $message = "Teste grátis ativado! Você tem acesso até {$endDate->format('d/m/Y')}.";

        if ($request->expectsJson()) {
            return response()->json([
                'message'     => $message,
                'enrollment'  => $enrollment,
                'end_date'    => $endDate->toDateString(),
                'days_left'   => $enrollment->daysLeft(),
            ]);
        }

        return redirect()->route('dashboard')->with('success', $message);
    }
}