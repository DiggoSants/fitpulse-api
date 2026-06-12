<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'user_id',
        'instructor_id',
        'biometric_id',
        'rfid_tag',
        'birth_date',
        'is_defaulter',
        'status',
        'renewed_at',
        'instructor_id',
        'goal',
        'custom_goal',
    ];

    protected $casts = [
        'is_defaulter' => 'boolean',
        'renewed_at'   => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function instructor()
    {
        return $this->belongsTo(Instructor::class);
    }

    public function workouts()
    {
        return $this->hasMany(Workout::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function renewals()
    {
        return $this->hasMany(PlanRenewal::class);
    }

    public function billings()
    {
        return $this->hasMany(Billing::class);
    }

    public function frequencies()
    {
        return $this->hasMany(Frequency::class);
    }

    public function activeEnrollment()
    {
        return $this->enrollments()
            ->where('status', 'active')
            ->where('end_date', '>=', now()->toDateString())
            ->latest('start_date')
            ->first();
    }

    public function isEnrolled(): bool
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
            ->exists();
    }

    public function hasAccess(): bool
    {
        return $this->status === 'active';
    }

    public function paymentStatus(): ?string
    {
        $status = $this->billings()->latest()->first()?->status;

        return match ($status) {
            'confirmed' => 'paid',
            'pending'   => 'pending',
            default     => $status,
        };
    }

    public function lastFrequency(): ?string
    {
        return $this->frequencies()->latest()->first()?->created_at?->format('d/m/Y H:i');
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isBlocked(): bool
    {
        return $this->status === 'blocked';
    }

    public function isDelinquent(): bool
    {
        return $this->status === 'delinquent';
    }

    public function block(): void
    {
        $this->update([
            'status'       => 'blocked',
            'is_defaulter' => true,
        ]);
    }

    public function markDelinquent(): void
    {
        $this->update([
            'status'       => 'delinquent',
            'is_defaulter' => true,
        ]);
    }

    public function activate(): void
    {
        $this->update([
            'status'       => 'active',
            'is_defaulter' => false,
        ]);
    }
    public static function getGoalOptions(): array
    {
        return [
            'hypertrophy'   => 'Hipertrofia (ganho de massa muscular)',
            'weight_loss'   => 'Emagrecimento (perda de peso)',
            'conditioning'  => 'Condicionamento físico',
            'health'        => 'Saúde e bem-estar',
            'rehabilitation' => 'Reabilitação (pós-lesão)',
            'other'         => 'Outro',
        ];
    }

    public function getGoalLabelAttribute(): string
    {
        return self::getGoalOptions()[$this->goal] ?? $this->goal;
    }
    // Verifica se o aluno já utilizou um teste grátis anteriormente
    public function hasUsedTrial(): bool
    {
        return $this->enrollments()
            ->whereHas('plan', function ($q) {
                $q->where('is_trial', true);
            })
            ->exists();
    }
}
