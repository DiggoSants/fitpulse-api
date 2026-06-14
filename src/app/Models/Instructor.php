<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Instructor extends Model
{
    protected $fillable = [
        'user_id',
        'specialty',
        'invite_code',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class)
            ->whereHas('user', function ($query) {
                $query->whereDoesntHave('manager')
                    ->whereDoesntHave('instructor')
                    ->whereDoesntHave('receptionist');
            });
    }

    public function workouts()
    {
        return $this->hasMany(Workout::class);
    }

    public static function generateInviteCode(): string
    {
        do {
            $code = strtoupper(Str::random(8));
        } while (self::where('invite_code', $code)->exists());

        return $code;
    }
    public function availability()
    {
        return $this->hasMany(InstructorAvailability::class);
    }

    // Buscar disponibilidade de um instrutor para um dia/turno específico
    public function isAvailableOn($weekDay, $shift = null)
    {
        $query = $this->availability()->where('week_day', $weekDay)->where('active', true);
        if ($shift) {
            $query->where('shift', $shift);
        }
        return $query->exists();
    }
    /**
     * Verifica se o instrutor está disponível para um aluno específico.
     */
    public function isAvailableForStudent(Student $student, string $shift = 'full_day'): bool
    {
        $studentSchedule = $student->user->schedule()
            ->where('active', true)
            ->get(['week_day', 'shift']);

        if ($studentSchedule->isEmpty()) {
            return false; // aluno sem agenda não pode ser vinculado
        }

        foreach ($studentSchedule as $schedule) {
            $scheduleShift = $schedule->shift ?: $shift;

            $hasAvailability = $this->availability()
                ->where('week_day', $schedule->week_day)
                ->where('shift', $scheduleShift)
                ->where('active', true)
                ->exists();

            if (!$hasAvailability) {
                return false;
            }
        }

        return true;
    }

    /**
     * Verifica se o instrutor atingiu o limite máximo de alunos (opcional).
     */
    public function hasReachedLimit(): bool
    {
        $limit = $this->max_students ?? 30; // valor padrão ou vindo do banco
        return $this->students()->count() >= $limit;
    }
}
