<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Exercise;

class ExerciseController extends Controller
{
    public function index()
    {
        $exercises = Exercise::all();
        return view('exercises.index', compact('exercises'));
    }

    public function create()
    {
        return view('exercises.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|min:3'
        ]);
        
        Exercise::create([
            'name' => $request->name,
            'description' => $request->description,
            'muscle_group' => $request->muscle_group
        ]);

        return redirect()->route('exercises.index');
    }

    public function edit($id)
    {
        $exercise = Exercise::findOrFail($id);
        return view('exercises.edit', compact('exercise'));
    }

    public function update(Request $request, $id)
    {
        $exercise = Exercise::findOrFail($id);

        $exercise->update([
            'name' => $request->name,
            'description' => $request->description,
            'muscle_group' => $request->muscle_group
        ]);

        return redirect()->route('exercises.index');
    }

    public function destroy($id)
    {
        $exercise = Exercise::findOrFail($id);
        $exercise->delete();

        return redirect()->route('exercises.index');
    }
}
