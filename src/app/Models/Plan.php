<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = [
        'name',
        'price',
        'duration_days',
    ];

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }
}