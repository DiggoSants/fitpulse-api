<?php

namespace App\Http\Controllers;

use App\Models\Frequency;
use App\Models\Instructor;
use App\Models\Student;
use App\Models\StudentSchedule;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FrequencyController extends Controller
{
    const POINTS_PER_DAY = 10;

    public function instructorStudents(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user || !$user->isInstructor()) {
            return response()->json([
                'message' => 'Apenas instrutores podem visualizar a frequencia dos alunos.',
            ], 403);
        }

        $data = $request->validate([
            'student_id' => ['nullable', 'integer'],
            'period'     => ['nullable', 'in:week,month,custom'],
            'start_date' => ['nullable', 'date'],
            'end_date'   => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);

        $period = $data['period'] ?? 'week';
        [$startDate, $endDate] = $this->resolveFrequencyPeriod($period, $data);

        $instructor = Instructor::where('user_id', $user->id)->firstOrFail();
        $weekDays = StudentSchedule::weekDays();

        $studentsQuery = Student::with([
            'user.schedule' => fn ($query) => $query->where('active', true),
            'frequencies' => fn ($query) => $query
                ->whereBetween('created_at', [$startDate->copy()->startOfDay(), $endDate->copy()->endOfDay()])
                ->orderBy('created_at'),
        ])->where('instructor_id', $instructor->id);

        if (!empty($data['student_id'])) {
            $studentsQuery->where('id', $data['student_id']);
        }

        $periodDays = collect(CarbonPeriod::create($startDate, $endDate));
        $totals = ['present' => 0, 'absent' => 0, 'no_record' => 0];

        $students = $studentsQuery
            ->get()
            ->sortBy(fn (Student $student) => mb_strtolower($student->user?->name ?? ''))
            ->values()
            ->map(function (Student $student) use ($periodDays, $weekDays, &$totals) {
                $scheduleDays = $student->user?->schedule
                    ? $student->user->schedule->pluck('week_day')->values()
                    : collect();

                $frequenciesByDate = $student->frequencies->groupBy(
                    fn (Frequency $frequency) => $frequency->created_at->toDateString()
                );

                $studentTotals = ['present' => 0, 'absent' => 0, 'no_record' => 0];

                $records = $periodDays->map(function (Carbon $date) use ($scheduleDays, $frequenciesByDate, $weekDays, &$studentTotals, &$totals) {
                    $dateKey = $date->toDateString();
                    $weekDay = strtolower($date->englishDayOfWeek);
                    $dayFrequencies = $frequenciesByDate->get($dateKey, collect());

                    if ($dayFrequencies->isNotEmpty()) {
                        $status = 'present';
                        $label = 'Presença';
                        $registeredAt = $dayFrequencies->first()->created_at->format('H:i');
                    } elseif ($scheduleDays->contains($weekDay) && $date->lessThanOrEqualTo(today())) {
                        $status = 'absent';
                        $label = 'Ausência';
                        $registeredAt = null;
                    } else {
                        $status = 'no_record';
                        $label = 'Sem registro';
                        $registeredAt = null;
                    }

                    $studentTotals[$status]++;
                    $totals[$status]++;

                    return [
                        'date'          => $dateKey,
                        'date_label'    => $date->format('d/m'),
                        'day_label'     => $weekDays[$weekDay] ?? $date->translatedFormat('l'),
                        'scheduled'     => $scheduleDays->contains($weekDay),
                        'status'        => $status,
                        'status_label'  => $label,
                        'registered_at' => $registeredAt,
                    ];
                })->values();

                $lastFrequency = $student->frequencies->sortByDesc('created_at')->first();

                return [
                    'id'             => $student->id,
                    'name'           => $student->user?->name ?? 'Aluno',
                    'email'          => $student->user?->email,
                    'schedule_days'  => $scheduleDays->values(),
                    'last_frequency' => $lastFrequency
                        ? $lastFrequency->created_at->format('d/m/Y H:i')
                        : null,
                    'totals'         => $studentTotals,
                    'records'        => $records,
                ];
            })
            ->values();

        return response()->json([
            'period'     => $period,
            'start_date' => $startDate->toDateString(),
            'end_date'   => $endDate->toDateString(),
            'totals'     => $totals,
            'students'   => $students,
            'message'    => $students->isEmpty()
                ? 'Nenhum aluno encontrado para este filtro.'
                : null,
        ]);
    }

    public function register()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user->isStudent()) {
            return response()->json([
                'message' => 'Apenas alunos podem registrar presença.',
            ], 403);
        }

        $student = Student::where('user_id', $user->id)->firstOrFail();

        if (!$student->hasAccess()) {
            $message = $student->isBlocked()
                ? 'Seu acesso está bloqueado. Entre em contato com a academia.'
                : 'Seu acesso está suspenso por inadimplência. Regularize seu pagamento.';

            return response()->json(['message' => $message], 403);
        }

        $activeEnrollment = $student->activeEnrollment();

        if (!$activeEnrollment) {
            return response()->json([
                'message' => 'Você não possui matrícula ativa.',
            ], 403);
        }

        [$frequency, $pointsEarned, $status] = DB::transaction(function () use ($student, $activeEnrollment, $user) {
            Student::whereKey($student->id)->lockForUpdate()->firstOrFail();

            $existingToday = Frequency::where('student_id', $student->id)
                ->where(function ($query) use ($activeEnrollment) {
                    $query->where('enrollment_id', $activeEnrollment->id)
                        ->orWhere(function ($legacyQuery) use ($activeEnrollment) {
                            $legacyQuery->whereNull('enrollment_id')
                                ->where('created_at', '>=', $activeEnrollment->created_at);
                        });
                })
                ->whereDate('created_at', today())
                ->first();

            if ($existingToday) {
                return [$existingToday, 0, 200];
            }

            $frequency = Frequency::create([
                'student_id'    => $student->id,
                'enrollment_id' => $activeEnrollment->id,
            ]);

            $user->addPoints(self::POINTS_PER_DAY);

            return [$frequency, self::POINTS_PER_DAY, 201];
        });

        $user->refresh();

        if ($pointsEarned === 0) {
            return response()->json([
                'message' => 'Presença já registrada hoje.',
                'data'    => [
                    'registered_at' => $frequency->created_at->format('d/m/Y H:i:s'),
                    'points_earned' => 0,
                    'total_points'  => $user->points,
                    'points_to_next'=> $user->pointsToNextReward(),
                ],
            ]);
        }

        return response()->json([
            'message' => 'Presença registrada com sucesso!',
            'data'    => [
                'registered_at' => $frequency->created_at->format('d/m/Y H:i:s'),
                'points_earned' => $pointsEarned,
                'total_points'  => $user->points,
                'points_to_next'=> $user->pointsToNextReward(),
            ],
        ], $status);
    }

    public function heatmap()
    {
        $frequencies = Frequency::selectRaw('
                DAYOFWEEK(created_at) - 1 as day_of_week,
                HOUR(created_at) as hour,
                COUNT(*) as count
            ')
            ->groupBy('day_of_week', 'hour')
            ->orderBy('day_of_week')
            ->orderBy('hour')
            ->get()
            ->map(function ($item) {
                $days = ['Domingo', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado'];

                return [
                    'day_of_week' => (int) $item->day_of_week,
                    'day_name'    => $days[$item->day_of_week],
                    'hour'        => (int) $item->hour,
                    'hour_label'  => sprintf('%02d:00', $item->hour),
                    'count'       => (int) $item->count,
                ];
            });

        $days   = ['Domingo', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado'];
        $matrix = [];

        for ($day = 0; $day < 7; $day++) {
            for ($hour = 0; $hour < 24; $hour++) {
                $found = $frequencies->first(function ($item) use ($day, $hour) {
                    return $item['day_of_week'] === $day && $item['hour'] === $hour;
                });

                $matrix[] = [
                    'day_of_week' => $day,
                    'day_name'    => $days[$day],
                    'hour'        => $hour,
                    'hour_label'  => sprintf('%02d:00', $hour),
                    'count'       => $found ? $found['count'] : 0,
                ];
            }
        }

        return response()->json(['data' => $matrix]);
    }

    private function resolveFrequencyPeriod(string $period, array $data): array
    {
        if ($period === 'custom') {
            $startDate = !empty($data['start_date'])
                ? Carbon::parse($data['start_date'])
                : today()->subDays(6);
            $endDate = !empty($data['end_date'])
                ? Carbon::parse($data['end_date'])
                : today();
        } elseif ($period === 'month') {
            $startDate = today()->startOfMonth();
            $endDate = today()->endOfMonth();
        } else {
            $startDate = today()->startOfWeek(Carbon::MONDAY);
            $endDate = today()->endOfWeek(Carbon::SUNDAY);
        }

        if ($endDate->greaterThan(today())) {
            $endDate = today();
        }

        if ($startDate->greaterThan($endDate)) {
            $startDate = $endDate->copy();
        }

        if ($startDate->diffInDays($endDate) > 31) {
            $startDate = $endDate->copy()->subDays(31);
        }

        return [$startDate->startOfDay(), $endDate->startOfDay()];
    }
}
