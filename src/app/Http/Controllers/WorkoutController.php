<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Workout;
use App\Models\Exercise;
use App\Models\WorkoutExercise;
use App\Models\Student;
use App\Models\StudentSchedule;
use App\Models\WorkoutSession;
use App\Models\WorkoutSessionExercise;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class WorkoutController extends Controller
{
    private function weekDayOrderSql(): string
    {
        return "CASE week_day WHEN 'monday' THEN 1 WHEN 'tuesday' THEN 2 WHEN 'wednesday' THEN 3 WHEN 'thursday' THEN 4 WHEN 'friday' THEN 5 WHEN 'saturday' THEN 6 WHEN 'sunday' THEN 7 ELSE 8 END";
    }

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

    private function scheduleDaysFor(Student $student): array
    {
        return $student->user->schedule()
            ->where('active', true)
            ->orderByRaw($this->weekDayOrderSql())
            ->pluck('week_day')
            ->toArray();
    }

    private function todayWorkoutSessionFor(Student $student, array $scheduleDays, int $userId): array
    {
        $todayWeekDay = strtolower(now()->englishDayOfWeek);

        $data = [
            'todayWorkout' => null,
            'todaySession' => null,
            'todaySessionExercises' => collect(),
            'todaySessionMessage' => 'Hoje não é dia de treino de acordo com sua agenda.',
            'todayWeekDay' => $todayWeekDay,
        ];

        if (!in_array($todayWeekDay, $scheduleDays, true)) {
            return $data;
        }

        $todayWorkout = Workout::where('student_id', $student->id)
            ->where('week_day', $todayWeekDay)
            ->with('workoutExercises.exercise')
            ->latest()
            ->first();

        $hasWorkoutsWithDay = Workout::where('student_id', $student->id)
            ->whereNotNull('week_day')
            ->exists();

        if (!$todayWorkout && !$hasWorkoutsWithDay) {
            $todayWorkout = Workout::where('student_id', $student->id)
                ->with('workoutExercises.exercise')
                ->latest()
                ->first();
        }

        if (!$todayWorkout) {
            $data['todaySessionMessage'] = 'Não há treino cadastrado para este dia da sua agenda.';
            return $data;
        }

        $todayWorkout->loadMissing('workoutExercises.exercise');

        $todaySession = WorkoutSession::firstOrCreate(
            [
                'workout_id' => $todayWorkout->id,
                'student_id' => $userId,
                'session_date' => today(),
            ],
            [
                'status' => 'pending',
                'total_exercises' => $todayWorkout->workoutExercises->count(),
                'completed_exercises' => 0,
            ]
        );

        $existingExerciseIds = $todaySession->sessionExercises()
            ->pluck('workout_exercise_id')
            ->toArray();

        foreach ($todayWorkout->workoutExercises as $workoutExercise) {
            if (!in_array($workoutExercise->id, $existingExerciseIds, true)) {
                WorkoutSessionExercise::create([
                    'workout_session_id' => $todaySession->id,
                    'workout_exercise_id' => $workoutExercise->id,
                    'completed' => false,
                ]);
            }
        }

        $totalExercises = $todayWorkout->workoutExercises()->count();
        $completedExercises = $todaySession->sessionExercises()->where('completed', true)->count();

        if ($todaySession->total_exercises !== $totalExercises || $todaySession->completed_exercises !== $completedExercises) {
            $todaySession->update([
                'total_exercises' => $totalExercises,
                'completed_exercises' => $completedExercises,
            ]);
        }

        $todaySession->load('workout', 'sessionExercises.workoutExercise.exercise');

        return array_merge($data, [
            'todayWorkout' => $todayWorkout,
            'todaySession' => $todaySession,
            'todaySessionExercises' => $todaySession->sessionExercises,
            'todaySessionMessage' => null,
        ]);
    }

    private function validateWorkoutDay(Request $request, Student $student): void
    {
        $scheduleDays = $this->scheduleDaysFor($student);

        if (count($scheduleDays) < StudentSchedule::MIN_DAYS) {
            throw ValidationException::withMessages([
                'week_day' => 'Defina a agenda semanal do aluno com o minimo exigido antes de vincular um treino.',
            ]);
        }

        if (!in_array($request->input('week_day'), $scheduleDays, true)) {
            throw ValidationException::withMessages([
                'week_day' => 'Selecione um dia cadastrado na agenda semanal do aluno.',
            ]);
        }
    }

    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->isInstructor() || $user->isManager()) {
            return redirect()->route('dashboard');
        }

        $student = Student::where('user_id', $user->id)->firstOrFail();
        $weekDays        = StudentSchedule::weekDays();
        $scheduleDays    = $this->scheduleDaysFor($student);
        $todaySessionData = $this->todayWorkoutSessionFor($student, $scheduleDays, $user->id);

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

        $minScheduleDays = StudentSchedule::MIN_DAYS;
        $workoutsByDay   = $allWorkouts->groupBy('week_day');
        $workoutsWithoutDay = $allWorkouts->filter(fn ($item) => empty($item->week_day));
        $workoutHistory = WorkoutSession::with('workout')
            ->where('student_id', $user->id)
            ->where(function ($query) {
                $query->whereDate('session_date', '<', today())
                    ->orWhere('status', 'completed');
            })
            ->orderByDesc('session_date')
            ->orderByDesc('updated_at')
            ->take(12)
            ->get();

        return view('workouts.index', array_merge(compact(
            'workout',
            'exercises',
            'allWorkouts',
            'weekDays',
            'scheduleDays',
            'minScheduleDays',
            'workoutsByDay',
            'workoutsWithoutDay',
            'workoutHistory'
        ), $todaySessionData));
    }

    public function create(Request $request)
    {
        $exercises = Exercise::query()
            ->orderByRaw("CASE WHEN muscle_group IS NULL OR muscle_group = '' THEN 1 ELSE 0 END")
            ->orderBy('muscle_group')
            ->orderBy('name')
            ->get();

        $studentId       = $request->query('student_id');
        $student         = $this->resolveStudent($studentId ? (int) $studentId : null);
        $weekDays        = StudentSchedule::weekDays();
        $scheduleDays    = $this->scheduleDaysFor($student);
        $minScheduleDays = StudentSchedule::MIN_DAYS;

        return view('workouts.create', compact(
            'exercises',
            'student',
            'weekDays',
            'scheduleDays',
            'minScheduleDays'
        ));
    }

    public function show($id)
    {
        return redirect()->route('workouts.index', ['workout_id' => $id]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => ['required', 'min:3', 'regex:/^[A-Za-z0-9\s]+$/'],
            'week_day'    => ['required', 'string', Rule::in(array_keys(StudentSchedule::weekDays()))],
            'exercise_id' => ['required', 'array'],
            'sets.*'      => ['nullable', 'integer', 'min:1'],
            'reps.*'      => ['nullable', 'integer', 'min:1'],
            'rest_time.*' => ['nullable', 'integer', 'min:1'],
        ], [
            'name.required'        => 'O nome do treino é obrigatório',
            'name.min'             => 'O nome deve ter pelo menos 3 caracteres',
            'name.regex'           => 'Use apenas letras e números',
            'week_day.required'    => 'Selecione o dia da agenda semanal.',
            'week_day.in'          => 'Dia da agenda invalido.',
            'exercise_id.required' => 'Selecione pelo menos um exercício',
        ]);

        $studentId = $request->input('student_id');
        $student   = $this->resolveStudent($studentId ? (int) $studentId : null);
        $this->validateWorkoutDay($request, $student);

        $workout = Workout::create([
            'student_id' => $student->id,
            'name'       => $request->name,
            'week_day'   => $request->week_day,
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
        $workout = Workout::with('workoutExercises')->findOrFail($id);

        $exercises = Exercise::query()
            ->orderByRaw("CASE WHEN muscle_group IS NULL OR muscle_group = '' THEN 1 ELSE 0 END")
            ->orderBy('muscle_group')
            ->orderBy('name')
            ->get();

        $studentId       = $request->query('student_id');
        $student         = $this->resolveStudent($studentId ? (int) $studentId : null);
        $weekDays        = StudentSchedule::weekDays();
        $scheduleDays    = $this->scheduleDaysFor($student);
        $minScheduleDays = StudentSchedule::MIN_DAYS;

        abort_if($workout->student_id !== $student->id, 403, 'Este treino não pertence a este aluno.');

        return view('workouts.edit', compact(
            'workout',
            'exercises',
            'student',
            'weekDays',
            'scheduleDays',
            'minScheduleDays'
        ));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name'        => ['required', 'min:3', 'regex:/^[A-Za-z0-9\s]+$/'],
            'week_day'    => ['required', 'string', Rule::in(array_keys(StudentSchedule::weekDays()))],
            'exercise_id' => ['required', 'array'],
            'sets.*'      => ['nullable', 'integer', 'min:1'],
            'reps.*'      => ['nullable', 'integer', 'min:1'],
            'rest_time.*' => ['nullable', 'integer', 'min:1'],
        ], [
            'name.required'        => 'O nome do treino é obrigatório',
            'name.min'             => 'O nome deve ter pelo menos 3 caracteres',
            'name.regex'           => 'Use apenas letras e números',
            'week_day.required'    => 'Selecione o dia da agenda semanal.',
            'week_day.in'          => 'Dia da agenda invalido.',
            'exercise_id.required' => 'Selecione pelo menos um exercício',
        ]);

        $studentId = $request->input('student_id');
        $student   = $this->resolveStudent($studentId ? (int) $studentId : null);
        $this->validateWorkoutDay($request, $student);

        $workout = Workout::findOrFail($id);
        abort_if($workout->student_id !== $student->id, 403, 'Este treino não pertence a este aluno.');

        $workout->update([
            'name'     => $request->name,
            'week_day' => $request->week_day,
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

    public function getMuscleGroups(Request $request)
    {
        $groups = Exercise::select('muscle_group')
            ->distinct()
            ->whereNotNull('muscle_group')
            ->where('muscle_group', '!=', '')
            ->orderBy('muscle_group')
            ->pluck('muscle_group');

        $groupNames = [
            'chest'    => 'Peito',
            'back'     => 'Costas',
            'legs'     => 'Pernas',
            'shoulders'=> 'Ombros',
            'biceps'   => 'Bíceps',
            'triceps'  => 'Tríceps',
            'abs'      => 'Abdômen',
            'glutes'   => 'Glúteos',
            'calves'   => 'Panturrilha',
            'traps'    => 'Trapézio',
            'forearms' => 'Antebraço',
            'cardio'   => 'Cardio',
        ];

        $formatted = [];
        foreach ($groups as $group) {
            $formatted[] = [
                'value' => $group,
                'label' => $groupNames[$group] ?? ucfirst($group),
            ];
        }

        if ($request->wantsJson()) {
            return response()->json($formatted);
        }

        return $formatted;
    }

    public function filterExercisesByMuscleGroup(Request $request)
    {
        $request->validate([
            'muscle_groups'   => ['required', 'array', 'min:1'],
            'muscle_groups.*' => ['string'],
        ]);

        $exercises = Exercise::whereIn('muscle_group', $request->muscle_groups)
            ->orderBy('muscle_group')
            ->orderBy('name')
            ->get();

        $groupNames = [
            'chest'    => 'Peito',
            'back'     => 'Costas',
            'legs'     => 'Pernas',
            'shoulders'=> 'Ombros',
            'biceps'   => 'Bíceps',
            'triceps'  => 'Tríceps',
            'abs'      => 'Abdômen',
            'glutes'   => 'Glúteos',
            'calves'   => 'Panturrilha',
            'traps'    => 'Trapézio',
            'forearms' => 'Antebraço',
            'cardio'   => 'Cardio',
        ];

        $grouped = [];
        foreach ($exercises as $exercise) {
            $groupKey  = $exercise->muscle_group;
            $groupName = $groupNames[$groupKey] ?? ucfirst($groupKey);

            if (!isset($grouped[$groupName])) {
                $grouped[$groupName] = [];
            }

            $grouped[$groupName][] = [
                'id'             => $exercise->id,
                'name'           => $exercise->name,
                'muscle_group'   => $exercise->muscle_group,
                'muscle_group_pt'=> $groupName,
            ];
        }

        return response()->json([
            'total'                 => $exercises->count(),
            'selected_muscle_groups'=> $request->muscle_groups,
            'exercises'             => $grouped,
        ]);
    }

    public function getStudentWorkouts($studentId)
    {
        $student  = Student::findOrFail($studentId);
        $workouts = Workout::where('student_id', $student->id)
            ->with('workoutExercises.exercise')
            ->get();

        return response()->json($workouts);
    }
}
