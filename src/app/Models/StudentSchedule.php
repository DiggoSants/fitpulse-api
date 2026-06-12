<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentSchedule extends Model
{
    public const MIN_DAYS = 2;

    protected $table = 'student_schedules';
    
    protected $fillable = ['user_id', 'week_day', 'active'];
    
    protected $casts = [
        'active' => 'boolean',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    // Dias da semana disponíveis (para exibir em português)
    public static function weekDays()
    {
        return [
            'monday' => 'Segunda-feira',
            'tuesday' => 'Terça-feira',
            'wednesday' => 'Quarta-feira',
            'thursday' => 'Quinta-feira',
            'friday' => 'Sexta-feira',
            'saturday' => 'Sábado',
            'sunday' => 'Domingo',
        ];
    }
    
    // Dias em português para validação (mapeamento)
    public static function weekDaysPtBr()
    {
        return [
            'segunda' => 'monday',
            'terca' => 'tuesday',
            'quarta' => 'wednesday',
            'quinta' => 'thursday',
            'sexta' => 'friday',
            'sabado' => 'saturday',
            'domingo' => 'sunday',
        ];
    }
}
