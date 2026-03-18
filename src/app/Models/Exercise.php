<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\WorkoutExercise;

class Exercise extends Model
{
    protected $fillable = [
        'name',
        'description',
        'muscle_group'
    ];

    public function workoutExercises()
    {
        return $this->hasMany(WorkoutExercise::class);
    }
}