<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Workout;
use App\Models\Exercise;
use App\Models\WorkoutExercise;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;

class WorkoutController extends Controller
{
    private function resolveStudent(?int $studentId = null): Student
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($studentId && ($user->isInstructor() || $user->isManager())) {
            $student = Student::findOrFail($studentId);
            if ($user->isInstructor()) {
                $instructor = $user->instructor;

                if ($student->instructor_id === null) {
                    $student->forceFill(['instructor_id' => $instructor->id])->save();
                }

                abort_if($student->instructor_id !== $instructor->id, 403, 'Voce nao pode alterar o treino deste aluno.');
            }

            return $student;
        }

        $student = Student::where('user_id', $user->id)->first();
        abort_if(!$student, 403, 'Aluno não encontrado.');

        return $student;
    }

    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user    = Auth::user();

        if ($user->isInstructor() || $user->isManager()) {
            return redirect()->route('dashboard');
        }

        $student = Student::where('user_id', $user->id)->firstOrFail();

        $allWorkouts = Workout::with('workoutExercises')
            ->where('student_id', $student->id)
            ->latest()
            ->get();

        $workoutId = $request->query('workout_id');
        $workout   = $workoutId
            ? Workout::where('student_id', $student->id)->where('id', $workoutId)->first()
            : $allWorkouts->first();

        $exercises = $workout
            ? WorkoutExercise::with('exercise')->where('workout_id', $workout->id)->get()
            : collect();

        return view('workouts.index', compact('workout', 'exercises', 'allWorkouts'));
    }

    public function create(Request $request)
    {
        // Buscar grupos musculares distintos
        $muscleGroups = Exercise::select('muscle_group')
            ->distinct()
            ->whereNotNull('muscle_group')
            ->where('muscle_group', '!=', '')
            ->orderBy('muscle_group')
            ->get()
            ->pluck('muscle_group');
        
        $groupNames = [
            'chest' => 'Peito',
            'back' => 'Costas',
            'legs' => 'Pernas',
            'shoulders' => 'Ombros',
            'biceps' => 'Bíceps',
            'triceps' => 'Tríceps',
            'abs' => 'Abdômen',
            'glutes' => 'Glúteos',
            'calves' => 'Panturrilha',
            'traps' => 'Trapézio',
            'forearms' => 'Antebraço',
            'cardio' => 'Cardio',
        ];
        
        $formattedGroups = [];
        foreach ($muscleGroups as $group) {
            $formattedGroups[$group] = $groupNames[$group] ?? ucfirst($group);
        }
        
        $exercises = Exercise::query()
            ->orderByRaw("CASE WHEN muscle_group IS NULL OR muscle_group = '' THEN 1 ELSE 0 END")
            ->orderBy('muscle_group')
            ->orderBy('name')
            ->get();
        
        // Agrupar exercícios por grupo muscular
        $exercisesByGroup = [];
        foreach ($exercises as $exercise) {
            $group = $exercise->muscle_group ?: 'outros';
            if (!isset($exercisesByGroup[$group])) {
                $exercisesByGroup[$group] = [];
            }
            $exercisesByGroup[$group][] = $exercise;
        }
        
        $studentId = $request->query('student_id');
        $student   = $this->resolveStudent($studentId ? (int) $studentId : null);

        return view('workouts.create', compact('exercises', 'student', 'formattedGroups', 'exercisesByGroup'));
    }

    public function show($id)
    {
        return redirect()->route('workouts.index', ['workout_id' => $id]);
    }

    public function store(Request $request)
    {
        // Validação com grupos musculares
        $request->validate([
            'name'           => ['required', 'min:3', 'regex:/^[A-Za-z0-9\s]+$/'],
            'muscle_groups'  => ['required', 'array', 'min:1'],
            'muscle_groups.*'=> ['string'],
            'exercise_id'    => ['required', 'array'],
            'sets.*'         => ['nullable', 'integer', 'min:1'],
            'reps.*'         => ['nullable', 'integer', 'min:1'],
            'rest_time.*'    => ['nullable', 'integer', 'min:1'],
        ], [
            'name.required'           => 'O nome do treino é obrigatório',
            'name.min'                => 'O nome deve ter pelo menos 3 caracteres',
            'name.regex'              => 'Use apenas letras e números',
            'muscle_groups.required'  => 'Selecione pelo menos um grupo muscular',
            'muscle_groups.min'       => 'Você deve selecionar pelo menos um grupo muscular',
            'exercise_id.required'    => 'Selecione pelo menos um exercício',
        ]);

        $studentId = $request->input('student_id');
        $student   = $this->resolveStudent($studentId ? (int) $studentId : null);

        // Verificar se os exercícios pertencem aos grupos selecionados
        $exerciseIds = $request->exercise_id;
        $exercisesInGroups = Exercise::whereIn('id', $exerciseIds)
            ->whereIn('muscle_group', $request->muscle_groups)
            ->count();
        
        if ($exercisesInGroups != count($exerciseIds)) {
            return back()->with('error', 'Um ou mais exercícios não pertencem aos grupos musculares selecionados.')->withInput();
        }

        // Criar treino com os grupos musculares
        $workout = Workout::create([
            'student_id'    => $student->id,
            'name'          => $request->name,
            'muscle_groups' => json_encode($request->muscle_groups),
        ]);

        $validExercise = false;

        foreach ($request->exercise_id as $exerciseId) {
            $sets = $request->sets[$exerciseId] ?? null;
            $reps = $request->reps[$exerciseId] ?? null;

            if (!isset($sets) || !isset($reps) || $sets <= 0 || $reps <= 0) {
                continue;
            }

            $validExercise = true;

            WorkoutExercise::create([
                'workout_id'  => $workout->id,
                'exercise_id' => $exerciseId,
                'sets'        => $sets,
                'reps'        => $reps,
                'rest_time'   => $request->rest_time[$exerciseId] ?? null,
            ]);
        }

        if (!$validExercise) {
            $workout->delete();
            return back()->with('error', 'Preencha séries e reps de pelo menos um exercício')->withInput();
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->isInstructor() || $user->isManager()) {
            return redirect()->route('dashboard');
        }

        return redirect()->route('workouts.index')->with('success', 'Treino criado com sucesso!');
    }

    public function edit(Request $request, $id)
    {
        $workout   = Workout::with('workoutExercises')->findOrFail($id);
        
        // Buscar grupos musculares distintos
        $muscleGroups = Exercise::select('muscle_group')
            ->distinct()
            ->whereNotNull('muscle_group')
            ->where('muscle_group', '!=', '')
            ->orderBy('muscle_group')
            ->get()
            ->pluck('muscle_group');
        
        $groupNames = [
            'chest' => 'Peito',
            'back' => 'Costas',
            'legs' => 'Pernas',
            'shoulders' => 'Ombros',
            'biceps' => 'Bíceps',
            'triceps' => 'Tríceps',
            'abs' => 'Abdômen',
            'glutes' => 'Glúteos',
            'calves' => 'Panturrilha',
            'traps' => 'Trapézio',
            'forearms' => 'Antebraço',
            'cardio' => 'Cardio',
        ];
        
        $formattedGroups = [];
        foreach ($muscleGroups as $group) {
            $formattedGroups[$group] = $groupNames[$group] ?? ucfirst($group);
        }
        
        $exercises = Exercise::query()
            ->orderByRaw("CASE WHEN muscle_group IS NULL OR muscle_group = '' THEN 1 ELSE 0 END")
            ->orderBy('muscle_group')
            ->orderBy('name')
            ->get();
        
        // Agrupar exercícios por grupo muscular
        $exercisesByGroup = [];
        foreach ($exercises as $exercise) {
            $group = $exercise->muscle_group ?: 'outros';
            if (!isset($exercisesByGroup[$group])) {
                $exercisesByGroup[$group] = [];
            }
            $exercisesByGroup[$group][] = $exercise;
        }
        
        $studentId = $request->query('student_id');
        $student   = $this->resolveStudent($studentId ? (int) $studentId : null);

        abort_if($workout->student_id !== $student->id, 403, 'Este treino não pertence a este aluno.');

        // Recuperar grupos musculares salvos
        $savedMuscleGroups = json_decode($workout->muscle_groups, true) ?? [];

        return view('workouts.edit', compact('workout', 'exercises', 'student', 'formattedGroups', 'exercisesByGroup', 'savedMuscleGroups'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name'           => ['required', 'min:3', 'regex:/^[A-Za-z0-9\s]+$/'],
            'muscle_groups'  => ['required', 'array', 'min:1'],
            'muscle_groups.*'=> ['string'],
            'exercise_id'    => ['required', 'array'],
            'sets.*'         => ['nullable', 'integer', 'min:1'],
            'reps.*'         => ['nullable', 'integer', 'min:1'],
            'rest_time.*'    => ['nullable', 'integer', 'min:1'],
        ], [
            'name.required'           => 'O nome do treino é obrigatório',
            'name.min'                => 'O nome deve ter pelo menos 3 caracteres',
            'name.regex'              => 'Use apenas letras e números',
            'muscle_groups.required'  => 'Selecione pelo menos um grupo muscular',
            'muscle_groups.min'       => 'Você deve selecionar pelo menos um grupo muscular',
            'exercise_id.required'    => 'Selecione pelo menos um exercício',
        ]);

        $studentId = $request->input('student_id');
        $student   = $this->resolveStudent($studentId ? (int) $studentId : null);

        $workout = Workout::findOrFail($id);
        abort_if($workout->student_id !== $student->id, 403, 'Este treino não pertence a este aluno.');

        // Verificar se os exercícios pertencem aos grupos selecionados
        $exerciseIds = $request->exercise_id;
        $exercisesInGroups = Exercise::whereIn('id', $exerciseIds)
            ->whereIn('muscle_group', $request->muscle_groups)
            ->count();
        
        if ($exercisesInGroups != count($exerciseIds)) {
            return back()->with('error', 'Um ou mais exercícios não pertencem aos grupos musculares selecionados.')->withInput();
        }

        $workout->update([
            'name'          => $request->name,
            'muscle_groups' => json_encode($request->muscle_groups),
        ]);

        WorkoutExercise::where('workout_id', $workout->id)->delete();

        $validExercise = false;

        foreach ($request->exercise_id as $exerciseId) {
            $sets = $request->sets[$exerciseId] ?? null;
            $reps = $request->reps[$exerciseId] ?? null;

            if (!isset($sets) || !isset($reps) || $sets <= 0 || $reps <= 0) {
                continue;
            }

            $validExercise = true;

            WorkoutExercise::create([
                'workout_id'  => $workout->id,
                'exercise_id' => $exerciseId,
                'sets'        => $sets,
                'reps'        => $reps,
                'rest_time'   => $request->rest_time[$exerciseId] ?? null,
            ]);
        }

        if (!$validExercise) {
            return back()->with('error', 'Preencha séries e reps de pelo menos um exercício')->withInput();
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->isInstructor() || $user->isManager()) {
            return redirect()->route('dashboard');
        }

        return redirect()->route('workouts.index')->with('success', 'Treino atualizado!');
    }

    public function destroy(Request $request, $id)
    {
        $workout   = Workout::findOrFail($id);
        $studentId = $request->input('student_id');
        $student   = $this->resolveStudent($studentId ? (int) $studentId : null);

        abort_if($workout->student_id !== $student->id, 403, 'Este treino não pertence a este aluno.');

        WorkoutExercise::where('workout_id', $id)->delete();
        $workout->delete();

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->isInstructor() || $user->isManager()) {
            return redirect()->route('dashboard');
        }

        return redirect()->route('workouts.index')->with('success', 'Treino deletado!');
    }

    // ==============================================
    // FILTRO POR GRUPO MUSCULAR
    // ==============================================

    /**
     * Get all available muscle groups from exercises
     */
    public function getMuscleGroups(Request $request)
    {
        $groups = Exercise::select('muscle_group')
            ->distinct()
            ->whereNotNull('muscle_group')
            ->where('muscle_group', '!=', '')
            ->orderBy('muscle_group')
            ->pluck('muscle_group');
        
        $groupNames = [
            'chest' => 'Peito',
            'back' => 'Costas',
            'legs' => 'Pernas',
            'shoulders' => 'Ombros',
            'biceps' => 'Bíceps',
            'triceps' => 'Tríceps',
            'abs' => 'Abdômen',
            'glutes' => 'Glúteos',
            'calves' => 'Panturrilha',
            'traps' => 'Trapézio',
            'forearms' => 'Antebraço',
            'cardio' => 'Cardio',
        ];
        
        $formatted = [];
        foreach ($groups as $group) {
            $formatted[] = [
                'value' => $group,
                'label' => $groupNames[$group] ?? ucfirst($group)
            ];
        }
        
        if ($request->wantsJson()) {
            return response()->json($formatted);
        }
        
        return $formatted;
    }

    /**
     * Filter exercises by muscle group (for API/JSON requests)
     */
    public function filterExercisesByMuscleGroup(Request $request)
    {
        $request->validate([
            'muscle_groups' => ['required', 'array', 'min:1'],
            'muscle_groups.*' => ['string']
        ]);
        
        $exercises = Exercise::whereIn('muscle_group', $request->muscle_groups)
            ->orderBy('muscle_group')
            ->orderBy('name')
            ->get();
        
        $groupNames = [
            'chest' => 'Peito',
            'back' => 'Costas',
            'legs' => 'Pernas',
            'shoulders' => 'Ombros',
            'biceps' => 'Bíceps',
            'triceps' => 'Tríceps',
            'abs' => 'Abdômen',
            'glutes' => 'Glúteos',
            'calves' => 'Panturrilha',
            'traps' => 'Trapézio',
            'forearms' => 'Antebraço',
            'cardio' => 'Cardio',
        ];
        
        // Group exercises by muscle group
        $grouped = [];
        foreach ($exercises as $exercise) {
            $groupKey = $exercise->muscle_group;
            $groupName = $groupNames[$groupKey] ?? ucfirst($groupKey);
            
            if (!isset($grouped[$groupName])) {
                $grouped[$groupName] = [];
            }
            
            $grouped[$groupName][] = [
                'id' => $exercise->id,
                'name' => $exercise->name,
                'muscle_group' => $exercise->muscle_group,
                'muscle_group_pt' => $groupName
            ];
        }
        
        return response()->json([
            'total' => $exercises->count(),
            'selected_muscle_groups' => $request->muscle_groups,
            'exercises' => $grouped
        ]);
    }
}