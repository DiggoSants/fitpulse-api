<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Enrollment extends Model
{
    protected $fillable = [
        'student_id',
        'plan_id',
        'start_date',
        'end_date',
        'status',
        'cancelled_at',
    ];

    protected $casts = [
        'start_date'   => 'date',
        'end_date'     => 'date',
        'cancelled_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function renewals()
    {
        return $this->hasMany(PlanRenewal::class, 'old_enrollment_id');
    }

    public function isActive(): bool
    {
        return $this->status === 'active'
            && ($this->end_date->isFuture() || $this->end_date->isToday());
    }

    public function cancel(): void
    {
        $this->update([
            'status'       => 'cancelled',
            'cancelled_at' => now(),
        ]);
    }
}