<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\WorkoutExercise;

class Exercise extends Model
{
    protected $fillable = [
        'name',
        'description',
        'muscle_group',
        'image_url',
        'video_url'
    ];

    public function workoutExercises()
    {
        return $this->hasMany(WorkoutExercise::class);
    }
}