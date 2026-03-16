<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Workout;

class Student extends Model
{
    protected $fillable = [
        'user_id',
        'instructor_id',
        'biometric_id',
        'rfid_tag',
        'birth_date',
        'is_defaulter'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function workouts()
    {
        return $this->hasMany(Workout::class);
    }
}