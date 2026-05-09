<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

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
}