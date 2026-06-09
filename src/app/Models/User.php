<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\StudentSchedule;
use App\Models\WorkoutSession;
use App\Models\Attendance;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'points',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'points'            => 'integer',
        ];
    }

    // ── Relações ──────────────────────────────────────────────────────────────

    public function student()
    {
        return $this->hasOne(Student::class);
    }

    public function instructor()
    {
        return $this->hasOne(Instructor::class);
    }

    public function manager()
    {
        return $this->hasOne(Manager::class);
    }

    public function receptionist()
    {
        return $this->hasOne(Receptionist::class);
    }

    public function physicalEvaluations()
    {
        return $this->hasMany(PhysicalEvaluation::class);
    }

    public function ownedGroups()
    {
        return $this->hasMany(PlanGroup::class, 'owner_id');
    }

    public function planGroups()
    {
        return $this->belongsToMany(PlanGroup::class, 'plan_group_members');
    }

    // ── Agenda semanal ───────────────────────────────────────────────────────
    public function schedule()
    {
        return $this->hasMany(StudentSchedule::class);
    }

    // ── Sessões de treino ────────────────────────────────────────────────────
    public function workoutSessions()
    {
        return $this->hasMany(WorkoutSession::class, 'student_id');
    }

    // ── Helpers de papel ──────────────────────────────────────────────────────

    public function role(): string
    {
        if ($this->manager()->exists())      return 'manager';
        if ($this->instructor()->exists())   return 'instructor';
        if ($this->receptionist()->exists()) return 'receptionist';
        return 'student';
    }

    public function isManager(): bool
    {
        return $this->role() === 'manager';
    }

    public function isInstructor(): bool
    {
        return $this->role() === 'instructor';
    }

    public function isReceptionist(): bool
    {
        return $this->role() === 'receptionist';
    }

    public function isStudent(): bool
    {
        return $this->role() === 'student';
    }

    // ── Helpers de gamificação ────────────────────────────────────────────────

    public function addPoints(int $points): void
    {
        $this->increment('points', $points);
    }

    public function hasGamificationBonus(): bool
    {
        return $this->points >= 100;
    }

    public function gamificationBonus(): float
    {
        return $this->hasGamificationBonus() ? 5.0 : 0.0;
    }

    public function pointsToNextReward(): int
    {
        $threshold = 100;
        $remainder = $this->points % $threshold;
        return $remainder === 0 ? 0 : $threshold - $remainder;
    }

    /**
     * Verifica se o aluno treina hoje, baseado na agenda semanal.
     */
    public function trainsToday(): bool
    {
        $today = strtolower(now()->format('l'));

        $weekDays = [
            'monday'    => 'monday',
            'tuesday'   => 'tuesday',
            'wednesday' => 'wednesday',
            'thursday'  => 'thursday',
            'friday'    => 'friday',
            'saturday'  => 'saturday',
            'sunday'    => 'sunday',
        ];

        $todayWeekDay = $weekDays[$today] ?? null;

        return StudentSchedule::where('user_id', $this->id)
            ->where('week_day', $todayWeekDay)
            ->where('active', true)
            ->exists();
    }

    /**
     * Calcula a frequência do aluno com base nos dias agendados e presenças.
     */
    public function calculateAttendance(): float
    {
        $totalScheduleDays = StudentSchedule::where('user_id', $this->id)->count();

        if (!method_exists($this, 'attendances')) {
            return 0.0;
        }

        $attendedDays = $this->attendances()->where('attended', true)->count();

        if ($totalScheduleDays == 0) {
            return 0.0;
        }

        return round(($attendedDays / $totalScheduleDays) * 100, 2);
    }

    /**
     * Retorna a sessão de treino de hoje (se existir).
     */
    public function getTodayWorkoutSession()
    {
        return $this->workoutSessions()
            ->where('session_date', today())
            ->first();
    }

    /**
     * Verifica se o aluno já finalizou um treino hoje.
     */
    public function hasTrainedToday(): bool
    {
        return $this->workoutSessions()
            ->where('session_date', today())
            ->where('status', 'completed')
            ->exists();
    }

    // Relacionamento com frequências (como aluno)
    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'student_id');
    }

    // Relacionamento com frequências marcadas por ele (instrutor/gerente)
    public function markedAttendances()
    {
        return $this->hasMany(Attendance::class, 'marked_by');
    }

    // Alunos vinculados a este instrutor
    public function students()
    {
        return $this->hasMany(Student::class, 'instructor_id');
    }

    /**
     * Calcula a fidelidade do aluno com base na agenda e presenças reais.
     *
     * @param string|null $startDate (Y-m-d)
     * @param string|null $endDate   (Y-m-d)
     * @return array
     */
    public function calculateFidelity(?string $startDate = null, ?string $endDate = null): array
    {
        $scheduleDays = StudentSchedule::where('user_id', $this->id)
            ->where('active', true)
            ->pluck('week_day')
            ->toArray();

        if (empty($scheduleDays)) {
            return [
                'fidelity_rate' => null,
                'total_expected' => 0,
                'total_present' => 0,
                'message' => 'Aluno sem agenda definida. Não é possível calcular fidelidade.'
            ];
        }

        if (!$startDate) {
            $startDate = now()->startOfMonth()->toDateString();
        }
        if (!$endDate) {
            $endDate = now()->endOfMonth()->toDateString();
        }

        $start = \Carbon\Carbon::parse($startDate);
        $end   = \Carbon\Carbon::parse($endDate);

        $weekDaysMap = [
            'monday' => 1,
            'tuesday' => 2,
            'wednesday' => 3,
            'thursday' => 4,
            'friday' => 5,
            'saturday' => 6,
            'sunday' => 7
        ];
        $scheduleNumeric = array_map(fn($d) => $weekDaysMap[$d] ?? null, $scheduleDays);
        $scheduleNumeric = array_filter($scheduleNumeric);

        $totalExpected = 0;
        $current = $start->copy();
        while ($current <= $end) {
            $weekDayNum = (int) $current->format('N');
            if (in_array($weekDayNum, $scheduleNumeric)) {
                $totalExpected++;
            }
            $current->addDay();
        }

        if ($totalExpected === 0) {
            return [
                'fidelity_rate' => 0,
                'total_expected' => 0,
                'total_present' => 0,
                'message' => 'Nenhum dia de treino previsto no período selecionado.'
            ];
        }

        $attendances = Attendance::where('student_id', $this->id)
            ->whereBetween('attendance_date', [$startDate, $endDate])
            ->where('status', 'present')
            ->get();

        $totalPresent = 0;
        foreach ($attendances as $att) {
            $attDate = \Carbon\Carbon::parse($att->attendance_date);
            $weekDayNum = (int) $attDate->format('N');
            if (in_array($weekDayNum, $scheduleNumeric)) {
                $totalPresent++;
            }
        }

        $fidelityRate = round(($totalPresent / $totalExpected) * 100, 2);

        return [
            'fidelity_rate' => $fidelityRate,
            'total_expected' => $totalExpected,
            'total_present' => $totalPresent,
            'message' => null
        ];
    }
    // Relacionamento com matrículas
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'student_id');
    }

    /**
     * Retorna a matrícula ativa do aluno (que ainda permite acesso)
     */
    public function activeEnrollment()
    {
        return $this->enrollments()
            ->where(function ($q) {
                $q->where('status', 'active')
                    ->orWhere(function ($q2) {
                        $q2->where('status', 'cancelled')
                            ->where('end_date', '>', now());
                    });
            })
            ->where('end_date', '>=', now()->startOfDay())
            ->orderBy('end_date', 'desc')
            ->first();
    }

    /**
     * Verifica se o aluno tem acesso ativo (matrícula válida e dentro do prazo)
     */
    public function hasActiveAccess(): bool
    {
        $enrollment = $this->activeEnrollment();
        return $enrollment ? $enrollment->hasAccess() : false;
    }
}
