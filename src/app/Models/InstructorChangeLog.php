<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InstructorChangeLog extends Model
{
    protected $table = 'instructor_change_logs';

    protected $fillable = [
        'student_id',
        'old_instructor_id',
        'new_instructor_id',
        'changed_by',
        'reason',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function oldInstructor()
    {
        return $this->belongsTo(Instructor::class, 'old_instructor_id');
    }

    public function newInstructor()
    {
        return $this->belongsTo(Instructor::class, 'new_instructor_id');
    }

    public function changer()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}