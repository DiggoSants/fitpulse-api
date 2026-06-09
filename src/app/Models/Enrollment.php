<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Enrollment extends Model
{
    protected $fillable = [
        'student_id',
        'plan_id',
        'start_date',
        'end_date',
        'status',
        'cancelled_at',
    ];

    protected $casts = [
        'start_date'   => 'date',
        'end_date'     => 'date',
        'cancelled_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function renewals()
    {
        return $this->hasMany(PlanRenewal::class, 'old_enrollment_id');
    }

    /**
     * Verifica se a matrícula está ativa (status active E data fim >= hoje)
     */
    public function isActive(): bool
    {
        return $this->status === 'active' && $this->end_date->gte(now()->startOfDay());
    }

    /**
     * Verifica se o acesso deve ser permitido (considera cancelamento e data fim)
     */
    public function hasAccess(): bool
    {
        // Se a data fim já passou, não tem acesso
        if ($this->end_date->isPast()) {
            return false;
        }

        // Status active ou cancelled (desde que ainda no prazo) => acesso liberado
        return in_array($this->status, ['active', 'cancelled']);
    }

    /**
     * Retorna os dias restantes de acesso (0 se expirado)
     */
    public function daysLeft(): int
    {
        if ($this->end_date->isPast()) {
            return 0;
        }
        return now()->startOfDay()->diffInDays($this->end_date->startOfDay());
    }
}