<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Workout extends Model
{
    protected $fillable = ['name', 'student_id', 'instructor_id', 'muscle_groups', 'week_day'];
    
    protected $casts = [
        'muscle_groups' => 'array',
    ];
    
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
    
    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }
    
    public function exercises()
    {
        return $this->belongsToMany(Exercise::class, 'workout_exercises')
            ->withPivot('sets', 'repetitions')
            ->withTimestamps();
    }

    public function workoutExercises()
    {
        return $this->hasMany(WorkoutExercise::class);
    }
}
