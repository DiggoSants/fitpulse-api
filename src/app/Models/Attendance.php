<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'student_id', 'marked_by', 'attendance_date', 'status', 'notes'
    ];

    protected $casts = [
        'attendance_date' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function marker()
    {
        return $this->belongsTo(User::class, 'marked_by');
    }

    // Scope para filtrar por data
    public function scopeBetweenDates($query, $start, $end)
    {
        return $query->whereBetween('attendance_date', [$start, $end]);
    }
}