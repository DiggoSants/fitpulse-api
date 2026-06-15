<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'duration_days',
        'benefits',
        'status',
        'is_trial',
        'trial_days',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_trial' => 'boolean',
        'trial_days' => 'integer',
    ];

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeOrderedByPrice($query)
    {
        return $query->orderBy('price')->orderBy('name');
    }
    public function scopeTrials($query)
    {
        return $query->where('is_trial', true);
    }

    // Verifica se é um plano de teste
    public function isTrial(): bool
    {
        return $this->is_trial === true;
    }
}
