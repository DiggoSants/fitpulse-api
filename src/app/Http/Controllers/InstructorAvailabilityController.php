<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Instructor;
use App\Models\InstructorAvailability;
use Illuminate\Support\Facades\Auth;

class InstructorAvailabilityController extends Controller
{
    /**
     * Mostra a agenda do instrutor logado (para ele mesmo ver/editar)
     */
    public function index()
    {
        $user = Auth::user();
        $instructor = $user->instructor;
        
        if (!$instructor) {
            return redirect()->route('dashboard')->with('error', 'Apenas instrutores podem acessar.');
        }
        
        $instructor->load(['students.user.schedule']);

        $availabilities = InstructorAvailability::where('instructor_id', $instructor->id)
            ->orderByRaw("FIELD(week_day, 'monday','tuesday','wednesday','thursday','friday','saturday','sunday')")
            ->get();
        
        $weekDays = InstructorAvailability::weekDaysLabels();
        $shifts   = InstructorAvailability::shiftLabels();
        $agenda   = $this->buildAgendaPayload($instructor, $availabilities, $weekDays, $shifts);
        
        return view('instructors.availability', compact('agenda', 'availabilities', 'weekDays', 'shifts'));
    }
    
    /**
     * Armazena ou atualiza disponibilidade (para o instrutor logado)
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $instructor = $user->instructor;
        
        if (!$instructor) {
            return response()->json(['message' => 'Acesso negado.'], 403);
        }
        
        $request->validate([
            'availability' => ['required', 'array'],
            'availability.*.week_day' => ['required', 'string', 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday'],
            'availability.*.shift'    => ['required', 'string', 'in:morning,afternoon,evening,full_day'],
            'availability.*.active'   => ['nullable', 'boolean'],
            'availability.*.start_time' => ['nullable', 'date_format:H:i'],
            'availability.*.end_time'   => ['nullable', 'date_format:H:i'],
        ]);
        
        // Remove os antigos e recria (ou atualiza)
        InstructorAvailability::where('instructor_id', $instructor->id)->delete();
        
        foreach ($request->availability as $item) {
            if (!($item['active'] ?? false)) {
                continue;
            }

            InstructorAvailability::create([
                'instructor_id' => $instructor->id,
                'week_day'      => $item['week_day'],
                'shift'         => $item['shift'],
                'start_time'    => ($item['start_time'] ?? null) ?: null,
                'end_time'      => ($item['end_time'] ?? null) ?: null,
                'active'        => true,
            ]);
        }
        
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Agenda salva com sucesso.']);
        }
        
        return back()->with('success', 'Disponibilidade atualizada.');
    }
    
    /**
     * API: lista instrutores disponíveis em um determinado dia/turno
     */
    public function availableInstructors(Request $request)
    {
        $request->validate([
            'week_day' => ['required', 'string', 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday'],
            'shift'    => ['nullable', 'string', 'in:morning,afternoon,evening,full_day'],
        ]);
        
        $weekDay = $request->week_day;
        $shift   = $request->shift ?? 'full_day';
        
        $instructors = Instructor::whereHas('availability', function ($q) use ($weekDay, $shift) {
            $q->where('week_day', $weekDay)
              ->where('shift', $shift)
              ->where('active', true);
        })->with('user')->get();
        
        $result = $instructors->map(function ($instructor) {
            return [
                'id'   => $instructor->id,
                'name' => $instructor->user->name,
                'email'=> $instructor->user->email,
            ];
        });
        
        return response()->json([
            'week_day' => $weekDay,
            'shift'    => $shift,
            'instructors' => $result,
        ]);
    }

    private function buildAgendaPayload(Instructor $instructor, $availabilities, array $weekDays, array $shifts): array
    {
        $availabilityByDay = $availabilities->groupBy('week_day');
        $studentsBySlot = $this->studentsBySlot($instructor, array_keys($weekDays));

        $days = collect($weekDays)->map(function (string $label, string $dayKey) use ($availabilityByDay, $studentsBySlot, $shifts) {
            $dayStudentsByShift = $studentsBySlot[$dayKey] ?? [];
            $dayStudents = collect($dayStudentsByShift)
                ->flatten(1)
                ->unique('id')
                ->values()
                ->all();

            $slots = ($availabilityByDay->get($dayKey) ?? collect())
                ->map(function (InstructorAvailability $availability) use ($dayKey, $label, $dayStudentsByShift, $shifts) {
                    $slotStudents = $this->studentsForSlot($dayStudentsByShift, $availability->shift);
                    $isOccupied = $availability->active && count($slotStudents) > 0;
                    $status = !$availability->active ? 'unavailable' : ($isOccupied ? 'occupied' : 'free');

                    return [
                        'id'           => $availability->id,
                        'day_key'      => $dayKey,
                        'day_label'    => $label,
                        'shift'        => $availability->shift,
                        'shift_label'  => $shifts[$availability->shift] ?? $availability->shift,
                        'time_label'   => $this->timeLabel($availability, $shifts),
                        'active'       => $availability->active,
                        'status'       => $status,
                        'status_label' => match ($status) {
                            'occupied'    => 'Ocupado',
                            'free'        => 'Livre',
                            default       => 'Indisponível',
                        },
                        'students'     => $availability->active ? $slotStudents : [],
                    ];
                })
                ->values()
                ->all();

            return [
                'key'          => $dayKey,
                'label'        => $label,
                'short_label'  => mb_substr($label, 0, 3),
                'has_schedule' => count($slots) > 0,
                'slots'        => $slots,
                'students'     => $dayStudents,
            ];
        })->values()->all();

        $slots = collect($days)->flatMap(fn (array $day) => $day['slots']);

        return [
            'instructor' => [
                'name'      => $instructor->user?->name,
                'specialty' => $instructor->specialty ?: 'Instrutor',
            ],
            'summary' => [
                'registered_days' => collect($days)->where('has_schedule', true)->count(),
                'registered_slots'=> $slots->count(),
                'free_slots'      => $slots->where('status', 'free')->count(),
                'occupied_slots'  => $slots->where('status', 'occupied')->count(),
                'linked_students' => $instructor->students->count(),
            ],
            'next_slots' => $this->nextSlots($slots),
            'days'       => $days,
        ];
    }

    private function studentsBySlot(Instructor $instructor, array $weekDayKeys): array
    {
        $studentsBySlot = array_fill_keys($weekDayKeys, []);

        foreach ($instructor->students as $student) {
            $scheduledItems = $student->user?->schedule
                ->where('active', true) ?? collect();

            foreach ($scheduledItems as $schedule) {
                $day = $schedule->week_day;

                if (!array_key_exists($day, $studentsBySlot)) {
                    continue;
                }

                $shift = $schedule->shift ?: 'full_day';
                $studentsBySlot[$day][$shift] ??= [];
                $studentsBySlot[$day][$shift][] = [
                    'id'     => $student->id,
                    'name'   => $student->user?->name,
                    'email'  => $student->user?->email,
                    'status' => $student->is_defaulter ? 'Pendente' : 'Em dia',
                    'shift'  => $shift,
                ];
            }
        }

        return $studentsBySlot;
    }

    private function studentsForSlot(array $studentsByShift, string $shift): array
    {
        if ($shift === 'full_day') {
            return collect($studentsByShift)
                ->flatten(1)
                ->unique('id')
                ->values()
                ->all();
        }

        return collect($studentsByShift[$shift] ?? [])
            ->merge($studentsByShift['full_day'] ?? [])
            ->unique('id')
            ->values()
            ->all();
    }

    private function timeLabel(InstructorAvailability $availability, array $shifts): string
    {
        if ($availability->start_time && $availability->end_time) {
            return $this->formatTime($availability->start_time).' às '.$this->formatTime($availability->end_time);
        }

        return $shifts[$availability->shift] ?? $availability->shift;
    }

    private function formatTime($time): string
    {
        if ($time instanceof \Carbon\CarbonInterface) {
            return $time->format('H:i');
        }

        return mb_substr((string) $time, 0, 5);
    }

    private function nextSlots($slots)
    {
        $dayNumbers = [
            'sunday'    => 0,
            'monday'    => 1,
            'tuesday'   => 2,
            'wednesday' => 3,
            'thursday'  => 4,
            'friday'    => 5,
            'saturday'  => 6,
        ];
        $today = now()->dayOfWeek;

        return $slots
            ->where('active', true)
            ->map(function (array $slot) use ($dayNumbers, $today) {
                $daysUntil = (($dayNumbers[$slot['day_key']] ?? $today) - $today + 7) % 7;
                $slot['next_label'] = $daysUntil === 0 ? 'Hoje' : ($daysUntil === 1 ? 'Amanhã' : $slot['day_label']);
                $slot['days_until'] = $daysUntil;

                return $slot;
            })
            ->sortBy([
                ['days_until', 'asc'],
                ['shift', 'asc'],
            ])
            ->take(5)
            ->values()
            ->all();
    }
}
