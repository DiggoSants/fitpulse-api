<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhysicalEvaluation extends Model
{
    protected $fillable = [
        'user_id',
        'weight',
        'height',
        'body_fat',
        'notes',
    ];

    protected $casts = [
        'weight'   => 'decimal:2',
        'height'   => 'decimal:2',
        'body_fat' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getImcAttribute(): float
    {
        $heightM = $this->height / 100;
        return round($this->weight / ($heightM * $heightM), 2);
    }

    public function getImcClassificationAttribute(): string
    {
        $imc = $this->imc;

        return match (true) {
            $imc < 18.5 => 'Abaixo do peso',
            $imc < 25.0 => 'Peso normal',
            $imc < 30.0 => 'Sobrepeso',
            $imc < 35.0 => 'Obesidade grau I',
            $imc < 40.0 => 'Obesidade grau II',
            default     => 'Obesidade grau III',
        };
    }
}