<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Instructor;
use App\Models\InstructorAvailability;
use Illuminate\Support\Facades\Auth;

class InstructorAvailabilityController extends Controller
{
    /**
     * Mostra a agenda do instrutor logado (para ele mesmo ver/editar)
     */
    public function index()
    {
        $user = Auth::user();
        $instructor = $user->instructor;
        
        if (!$instructor) {
            return redirect()->route('dashboard')->with('error', 'Apenas instrutores podem acessar.');
        }
        
        $availabilities = InstructorAvailability::where('instructor_id', $instructor->id)
            ->orderByRaw("FIELD(week_day, 'monday','tuesday','wednesday','thursday','friday','saturday','sunday')")
            ->get();
        
        $weekDays = InstructorAvailability::weekDaysLabels();
        $shifts   = InstructorAvailability::shiftLabels();
        
        return view('instructors.availability', compact('availabilities', 'weekDays', 'shifts'));
    }
    
    /**
     * Armazena ou atualiza disponibilidade (para o instrutor logado)
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $instructor = $user->instructor;
        
        if (!$instructor) {
            return response()->json(['message' => 'Acesso negado.'], 403);
        }
        
        $request->validate([
            'availability' => ['required', 'array'],
            'availability.*.week_day' => ['required', 'string', 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday'],
            'availability.*.shift'    => ['required', 'string', 'in:morning,afternoon,evening,full_day'],
            'availability.*.active'   => ['boolean'],
        ]);
        
        // Remove os antigos e recria (ou atualiza)
        InstructorAvailability::where('instructor_id', $instructor->id)->delete();
        
        foreach ($request->availability as $item) {
            InstructorAvailability::create([
                'instructor_id' => $instructor->id,
                'week_day'      => $item['week_day'],
                'shift'         => $item['shift'],
                'active'        => $item['active'] ?? true,
            ]);
        }
        
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Agenda salva com sucesso.']);
        }
        
        return back()->with('success', 'Disponibilidade atualizada.');
    }
    
    /**
     * API: lista instrutores disponíveis em um determinado dia/turno
     */
    public function availableInstructors(Request $request)
    {
        $request->validate([
            'week_day' => ['required', 'string', 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday'],
            'shift'    => ['nullable', 'string', 'in:morning,afternoon,evening,full_day'],
        ]);
        
        $weekDay = $request->week_day;
        $shift   = $request->shift ?? 'full_day';
        
        $instructors = Instructor::whereHas('availability', function ($q) use ($weekDay, $shift) {
            $q->where('week_day', $weekDay)
              ->where('shift', $shift)
              ->where('active', true);
        })->with('user')->get();
        
        $result = $instructors->map(function ($instructor) {
            return [
                'id'   => $instructor->id,
                'name' => $instructor->user->name,
                'email'=> $instructor->user->email,
            ];
        });
        
        return response()->json([
            'week_day' => $weekDay,
            'shift'    => $shift,
            'instructors' => $result,
        ]);
    }
}