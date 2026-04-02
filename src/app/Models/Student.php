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

    public function activeEnrollment()
    {
        return $this->enrollments()
            ->where('end_date', '>=', now()->toDateString())
            ->latest('start_date')
            ->first();
    }

    public function isEnrolled(): bool
    {
        return $this->activeEnrollment() !== null;
    }
}