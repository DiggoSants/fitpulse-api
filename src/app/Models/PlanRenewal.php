<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanRenewal extends Model
{
    protected $fillable = [
        'student_id',
        'old_enrollment_id',
        'new_enrollment_id',
        'plan_id',
        'renewed_at',
    ];

    protected $casts = [
        'renewed_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function oldEnrollment()
    {
        return $this->belongsTo(Enrollment::class, 'old_enrollment_id');
    }

    public function newEnrollment()
    {
        return $this->belongsTo(Enrollment::class, 'new_enrollment_id');
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }
}
