<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanGroup extends Model
{
    protected $fillable = [
        'name',
        'owner_id',
        'plan_id',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'plan_group_members');
    }

    public function memberCount(): int
    {
        return $this->members()->count();
    }

    public function hasVacancy(): bool
    {
        return $this->memberCount() < 5;
    }

    /**
     * Calcula o desconto base pelo número de membros.
     * 2 membros → 5%
     * 3 membros → 10%
     * 4 membros → 15%
     * 5 membros → 20%
     */
    public function baseDiscount(): float
    {
        return match ($this->memberCount()) {
            2       => 5.0,
            3       => 10.0,
            4       => 15.0,
            5       => 20.0,
            default => 0.0,
        };
    }
}