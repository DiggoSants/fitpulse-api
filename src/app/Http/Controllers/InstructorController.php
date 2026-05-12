<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Instructor;
use App\Models\User;

class InstructorController extends Controller
{

    public function index()
    {
        $instructors = Instructor::with(['user', 'students'])->get();
        return view('instructors.index', compact('instructors'));
    }

    public function create()
{
    $users = User::whereDoesntHave('instructor')
        ->whereDoesntHave('manager')  // ← adiciona isso
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

        return redirect()->route('instructors.index')->with('success', 'Instrutor cadastrado com sucesso!');
    }

    public function show($id)
    {
        $instructor = Instructor::with(['user', 'students.user'])->findOrFail($id);
        return view('instructors.show', compact('instructor'));
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

        return redirect()->route('instructors.index')->with('success', 'Instrutor atualizado com sucesso!');
    }

    public function destroy($id)
    {
        $instructor = Instructor::findOrFail($id);
        $instructor->students()->update(['instructor_id' => null]);
        $instructor->delete();

        return redirect()->route('instructors.index')->with('success', 'Instrutor removido com sucesso!');
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
}
