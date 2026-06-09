<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InstructorAvailability extends Model
{
    protected $table = 'instructor_availability';
    
    protected $fillable = [
        'instructor_id', 'week_day', 'shift', 'start_time', 'end_time', 'active'
    ];
    
    protected $casts = [
        'active' => 'boolean',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
    ];
    
    public function instructor()
    {
        return $this->belongsTo(Instructor::class);
    }
    
    // Mapeamento para exibição amigável
    public static function weekDaysLabels()
    {
        return [
            'monday'    => 'Segunda-feira',
            'tuesday'   => 'Terça-feira',
            'wednesday' => 'Quarta-feira',
            'thursday'  => 'Quinta-feira',
            'friday'    => 'Sexta-feira',
            'saturday'  => 'Sábado',
            'sunday'    => 'Domingo',
        ];
    }
    
    public static function shiftLabels()
    {
        return [
            'morning'   => 'Manhã (08h às 12h)',
            'afternoon' => 'Tarde (13h às 18h)',
            'evening'   => 'Noite (18h às 22h)',
            'full_day'  => 'Dia inteiro',
        ];
    }
}