<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\StudentSchedule;
use App\Models\WorkoutSession;

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

    // ── Agenda semanal (Demanda 1) ───────────────────────────────────────────
    public function schedule()
    {
        return $this->hasMany(StudentSchedule::class);
    }

    // ── Sessões de treino (Demanda 3) ─────────────────────────────────────────
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
     * (Requer model Attendance; se não existir, retorna 0.)
     */
    public function calculateAttendance(): float
    {
        $totalScheduleDays = StudentSchedule::where('user_id', $this->id)->count();

        // Evita erro caso o relacionamento attendances ainda não exista
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
}