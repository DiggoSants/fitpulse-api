<?php

namespace App\Http\Controllers;

use App\Models\StudentSchedule;
use App\Models\User;
use App\Http\Requests\StudentScheduleRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentScheduleController extends Controller
{
    // Save student schedule
    public function store(StudentScheduleRequest $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'message' => 'Usuário não está autenticado.'
            ], 401);
        }
        
        // Remove old days
        StudentSchedule::where('user_id', $user->id)->delete();
        
        // Add new days
        foreach ($request->days as $day) {
            StudentSchedule::create([
                'user_id' => $user->id,
                'week_day' => $day,
                'active' => true
            ]);
        }
        
        return response()->json([
            'message' => 'Agenda salva com sucesso!',
            'days' => $request->days,
            'total_days' => count($request->days)
        ]);
    }
    
    // Get student schedule
    public function show($userId = null)
    {
        if ($userId === null) {
            $userId = Auth::id();
        }
        
        $userId = (int) $userId;
        
        $schedule = StudentSchedule::where('user_id', $userId)
            ->where('active', true)
            ->pluck('week_day')
            ->toArray();
        
        $weekDaysMap = StudentSchedule::weekDays();
        
        return response()->json([
            'days' => $schedule,
            'total_days' => count($schedule),
            'formatted_days' => array_map(function($day) use ($weekDaysMap) {
                return $weekDaysMap[$day] ?? $day;
            }, $schedule)
        ]);
    }
    
    // Validate student has at least 2 days
    public function validateSchedule($userId)
    {
        $userId = (int) $userId;
        
        $totalDays = StudentSchedule::where('user_id', $userId)
            ->where('active', true)
            ->count();
            
        if ($totalDays < 2) {
            return response()->json([
                'valid' => false,
                'message' => 'Aluno precisa ter pelo menos 2 dias de treino na semana.'
            ], 422);
        }
        
        return response()->json([
            'valid' => true,
            'total_days' => $totalDays
        ]);
    }
}