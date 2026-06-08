<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkoutSession extends Model
{
    protected $fillable = [
        'workout_id', 'student_id', 'session_date', 'started_at', 
        'completed_at', 'status', 'total_exercises', 'completed_exercises', 'notes'
    ];
    
    protected $casts = [
        'session_date' => 'date',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];
    
    public function workout()
    {
        return $this->belongsTo(Workout::class);
    }
    
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
    
    public function sessionExercises()
    {
        return $this->hasMany(WorkoutSessionExercise::class);
    }
    
    public function getProgressPercentageAttribute()
    {
        if ($this->total_exercises == 0) return 0;
        return round(($this->completed_exercises / $this->total_exercises) * 100);
    }
    
    public function isInProgress()
    {
        return $this->status === 'in_progress';
    }
    
    public function isCompleted()
    {
        return $this->status === 'completed';
    }
    
    public function canBeStarted()
    {
        return $this->status === 'pending';
    }
}