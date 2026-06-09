<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Instructor;
use App\Models\User;
use App\Models\Student;

class InstructorController extends Controller
{

    public function index()
    {
        return redirect()->route('dashboard');
    }

    public function create()
    {
        $users = User::whereDoesntHave('instructor')
            ->whereDoesntHave('manager')
            ->whereDoesntHave('receptionist')
            ->get();

        return view('instructors.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id'   => ['required', 'exists:users,id'],
            'specialty' => ['nullable', 'string', 'max:255'],
        ], [
            'user_id.required' => 'Selecione um usuário',
            'user_id.exists'   => 'Usuário inválido',
        ]);

        Instructor::create([
            'user_id'     => $request->user_id,
            'specialty'   => $request->specialty,
            'invite_code' => Instructor::generateInviteCode(),
        ]);

        return redirect()->route('dashboard')->with('success', 'Instrutor cadastrado com sucesso!');
    }

    public function show($id)
    {
        return redirect()->route('dashboard');
    }

    public function edit($id)
    {
        $instructor = Instructor::with('user')->findOrFail($id);
        return view('instructors.edit', compact('instructor'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'specialty' => ['nullable', 'string', 'max:255'],
        ]);

        $instructor = Instructor::findOrFail($id);
        $instructor->update([
            'specialty' => $request->specialty,
        ]);

        return redirect()->route('dashboard')->with('success', 'Instrutor atualizado com sucesso!');
    }

    public function destroy($id)
    {
        $instructor = Instructor::findOrFail($id);
        $instructor->students()->update(['instructor_id' => null]);
        $instructor->delete();

        return redirect()->route('dashboard')->with('success', 'Instrutor removido com sucesso!');
    }

    public function regenerateCode($id)
    {
        /** @var \App\Models\User $user */
        $user       = Auth::user();
        $instructor = Instructor::findOrFail($id);

        if ($user->isInstructor() && $user->instructor->id !== $instructor->id) {
            abort(403, 'Acesso não autorizado.');
        }

        $instructor->update([
            'invite_code' => Instructor::generateInviteCode(),
        ]);

        return back()->with('success', 'Código regenerado com sucesso!');
    }

    /**
     * Lista os alunos vinculados a este instrutor, incluindo o objetivo.
     */
    public function myStudents()
    {
        $instructor = Auth::user()->instructor;

        if (!$instructor) {
            if (request()->wantsJson()) {
                return response()->json(['message' => 'Você não é um instrutor.'], 403);
            }
            return redirect()->route('dashboard')->withErrors('Você não é um instrutor.');
        }

        $students = Student::with('user')
            ->where('instructor_id', $instructor->id)
            ->get()
            ->map(function ($student) {
                return [
                    'id'            => $student->id,
                    'user_id'       => $student->user_id,
                    'name'          => $student->user->name,
                    'email'         => $student->user->email,
                    'birth_date'    => $student->birth_date,
                    'is_defaulter'  => $student->is_defaulter,
                    'goal'          => $student->goal,
                    'goal_label'    => $this->getGoalLabel($student->goal),
                ];
            });

        if (request()->wantsJson()) {
            return response()->json($students);
        }

        return view('instructors.students', compact('students'));
    }

    /**
     * Retorna o rótulo em português para o objetivo do aluno.
     */
    private function getGoalLabel(?string $goal): string
    {
        $labels = [
            'hypertrophy'    => 'Hipertrofia (ganho de massa muscular)',
            'weight_loss'    => 'Emagrecimento (perda de peso)',
            'conditioning'   => 'Condicionamento físico',
            'health'         => 'Saúde e bem-estar',
            'rehabilitation' => 'Reabilitação (pós-lesão)',
            'other'          => 'Outro',
        ];

        return $labels[$goal] ?? 'Não definido';
    }
}