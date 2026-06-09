<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Student;
use App\Models\Instructor;
use App\Models\Manager;
use Illuminate\Support\Facades\Auth;

class InstructorAttendanceController extends Controller
{
    private function getInstructorId($user)
    {
        // Verifica se o usuário é instrutor
        $instructor = Instructor::where('user_id', $user->id)->first();
        if ($instructor) {
            return $instructor->id;
        }
        
        // Se for gerente, pode retornar null (para ver todos)
        $manager = Manager::where('user_id', $user->id)->first();
        if ($manager) {
            return null; // gerente vê todos
        }
        
        return null;
    }
    
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Verificar se é instrutor ou gerente
        $instructorRecord = Instructor::where('user_id', $user->id)->first();
        $managerRecord = Manager::where('user_id', $user->id)->first();
        
        if (!$instructorRecord && !$managerRecord) {
            return response()->json([
                'message' => 'Apenas instrutores e gerentes podem acessar esta funcionalidade.'
            ], 403);
        }
        
        // Buscar alunos vinculados ao instrutor
        if ($instructorRecord) {
            $students = Student::with('user')
                ->where('instructor_id', $instructorRecord->id)
                ->get();
        } else {
            // Gerente: pode ver todos ou filtrar por instrutor?
            if ($request->get('instructor_id')) {
                $students = Student::with('user')
                    ->where('instructor_id', $request->instructor_id)
                    ->get();
            } else {
                $students = Student::with('user')->get();
            }
        }
        
        $period = $request->get('period', 'month');
        $startDate = now();
        $endDate = now();
        
        switch ($period) {
            case 'week':
                $startDate = now()->startOfWeek();
                $endDate = now()->endOfWeek();
                break;
            case 'month':
                $startDate = now()->startOfMonth();
                $endDate = now()->endOfMonth();
                break;
            case 'custom':
                $startDate = $request->get('start_date') ? now()->parse($request->start_date) : now()->subDays(30);
                $endDate = $request->get('end_date') ? now()->parse($request->end_date) : now();
                break;
        }
        
        $result = [];
        foreach ($students as $student) {
            $userStudent = $student->user;
            if (!$userStudent) continue;
            
            $lastAttendance = Attendance::where('student_id', $userStudent->id)
                ->where('status', 'present')
                ->latest('attendance_date')
                ->first();
            
            $totalDaysInPeriod = $startDate->diffInDays($endDate) + 1;
            $attendancesCount = Attendance::where('student_id', $userStudent->id)
                ->whereBetween('attendance_date', [$startDate, $endDate])
                ->where('status', 'present')
                ->count();
            
            $frequencyRate = $totalDaysInPeriod > 0 ? round(($attendancesCount / $totalDaysInPeriod) * 100, 2) : 0;
            $daysSinceLastAttendance = $lastAttendance ? now()->diffInDays($lastAttendance->attendance_date) : null;
            
            if ($frequencyRate < 30) $statusLabel = 'critico';
            elseif ($frequencyRate < 60) $statusLabel = 'baixa';
            elseif ($frequencyRate >= 80) $statusLabel = 'excelente';
            else $statusLabel = 'regular';
            
            $result[] = [
                'student_id' => $userStudent->id,
                'student_name' => $userStudent->name,
                'student_email' => $userStudent->email,
                'instructor_id' => $student->instructor_id,
                'last_attendance' => $lastAttendance ? $lastAttendance->attendance_date->format('Y-m-d') : null,
                'days_since_last_attendance' => $daysSinceLastAttendance,
                'period_attendance_count' => $attendancesCount,
                'period_total_days' => $totalDaysInPeriod,
                'frequency_rate' => $frequencyRate,
                'status' => $statusLabel,
                'status_text' => $this->getStatusText($statusLabel),
            ];
        }
        
        usort($result, fn($a, $b) => $a['frequency_rate'] <=> $b['frequency_rate']);
        
        return response()->json([
            'period' => $period,
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'students' => $result,
        ]);
    }
    
    public function show($studentId)
    {
        $user = Auth::user();
        $instructorRecord = Instructor::where('user_id', $user->id)->first();
        $managerRecord = Manager::where('user_id', $user->id)->first();
        
        $student = Student::findOrFail($studentId);
        
        // Verificar permissão
        if (!$managerRecord && (!$instructorRecord || $student->instructor_id !== $instructorRecord->id)) {
            return response()->json(['message' => 'Aluno não pertence a você.'], 403);
        }
        
        $userStudent = $student->user;
        $startDate = now()->subDays(30);
        $attendances = Attendance::where('student_id', $userStudent->id)
            ->where('attendance_date', '>=', $startDate)
            ->orderBy('attendance_date', 'desc')
            ->get();
        
        $calendar = [];
        for ($date = clone $startDate; $date <= now(); $date->addDay()) {
            $dateStr = $date->format('Y-m-d');
            $att = $attendances->firstWhere('attendance_date', $dateStr);
            $calendar[] = [
                'date' => $dateStr,
                'status' => $att ? $att->status : 'absent',
                'notes' => $att ? $att->notes : null,
            ];
        }
        
        $totalPresent = $attendances->where('status', 'present')->count();
        
        return response()->json([
            'student' => [
                'id' => $userStudent->id,
                'name' => $userStudent->name,
                'email' => $userStudent->email,
                'instructor_id' => $student->instructor_id,
            ],
            'stats' => [
                'last_30_days_present' => $totalPresent,
                'last_30_days_absent' => $attendances->where('status', 'absent')->count(),
                'last_30_days_justified' => $attendances->where('status', 'justified')->count(),
                'attendance_rate' => round(($totalPresent / 30) * 100, 2),
            ],
            'calendar' => $calendar,
        ]);
    }
    
    public function markAttendance(Request $request, $studentId)
    {
        $request->validate([
            'attendance_date' => 'required|date|before_or_equal:today',
            'status' => 'required|in:present,absent,justified',
            'notes' => 'nullable|string|max:255',
        ]);
        
        $user = Auth::user();
        $instructorRecord = Instructor::where('user_id', $user->id)->first();
        $managerRecord = Manager::where('user_id', $user->id)->first();
        
        if (!$instructorRecord && !$managerRecord) {
            return response()->json(['message' => 'Apenas instrutores podem marcar presença.'], 403);
        }
        
        $student = Student::findOrFail($studentId);
        if (!$managerRecord && $student->instructor_id !== $instructorRecord->id) {
            return response()->json(['message' => 'Aluno não pertence a você.'], 403);
        }
        
        $attendance = Attendance::updateOrCreate(
            ['student_id' => $student->user_id, 'attendance_date' => $request->attendance_date],
            ['marked_by' => $user->id, 'status' => $request->status, 'notes' => $request->notes]
        );
        
        return response()->json(['message' => 'Frequência registrada.', 'attendance' => $attendance]);
    }
    
    public function absentStudents(Request $request)
    {
        $days = $request->get('days', 7);
        $user = Auth::user();
        $instructorRecord = Instructor::where('user_id', $user->id)->first();
        
        if (!$instructorRecord) {
            return response()->json(['message' => 'Apenas instrutores podem ver alunos ausentes.'], 403);
        }
        
        $students = Student::with('user')->where('instructor_id', $instructorRecord->id)->get();
        $cutoffDate = now()->subDays($days);
        $absentList = [];
        
        foreach ($students as $student) {
            $lastAttendance = Attendance::where('student_id', $student->user_id)
                ->where('status', 'present')
                ->latest('attendance_date')
                ->first();
            
            if (!$lastAttendance || $lastAttendance->attendance_date < $cutoffDate) {
                $absentList[] = [
                    'student_id' => $student->user_id,
                    'student_name' => $student->user->name,
                    'last_attendance' => $lastAttendance ? $lastAttendance->attendance_date->format('Y-m-d') : null,
                    'days_absent' => $lastAttendance ? now()->diffInDays($lastAttendance->attendance_date) : $days + 1,
                ];
            }
        }
        
        usort($absentList, fn($a, $b) => $b['days_absent'] <=> $a['days_absent']);
        
        return response()->json([
            'cutoff_days' => $days,
            'cutoff_date' => $cutoffDate->format('Y-m-d'),
            'total_absent' => count($absentList),
            'students' => $absentList,
        ]);
    }
    
    private function getStatusText($status)
    {
        switch ($status) {
            case 'excelente': return 'Excelente frequência!';
            case 'regular': return 'Frequência regular';
            case 'baixa': return 'Baixa frequência - precisa melhorar';
            case 'critico': return 'Frequência crítica! Risco de cancelamento';
            default: return 'Sem dados suficientes';
        }
    }
}