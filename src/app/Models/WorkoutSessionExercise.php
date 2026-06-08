<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkoutSessionExercise extends Model
{
    protected $fillable = [
        'workout_session_id', 'workout_exercise_id', 'completed', 
        'completed_at', 'actual_sets', 'actual_repetitions', 'notes'
    ];
    
    protected $casts = [
        'completed' => 'boolean',
        'completed_at' => 'datetime',
    ];
    
    public function workoutSession()
    {
        return $this->belongsTo(WorkoutSession::class);
    }
    
    public function workoutExercise()
    {
        return $this->belongsTo(WorkoutExercise::class);
    }
}