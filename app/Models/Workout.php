<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Student;
use App\Models\WorkoutExercise;

class Workout extends Model
{
    protected $fillable = [
        'student_id',
        'name'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function workoutExercises()
    {
        return $this->hasMany(WorkoutExercise::class);
    }
}