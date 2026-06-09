<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return redirect()->route('dashboard');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id'       => 'required|exists:users,id',
            'instructor_id' => 'nullable|exists:instructors,id',
            'biometric_id'  => 'nullable|string|max:255',
            'rfid_tag'      => 'nullable|string|max:255',
            'birth_date'    => 'nullable|date',
            'is_defaulter'  => 'nullable|boolean',
            'goal'          => 'nullable|in:hypertrophy,weight_loss,conditioning,health,rehabilitation,other',
        ]);

        Student::create([
            'user_id'       => $request->user_id,
            'instructor_id' => $request->instructor_id,
            'biometric_id'  => $request->biometric_id,
            'rfid_tag'      => $request->rfid_tag,
            'birth_date'    => $request->birth_date,
            'is_defaulter'  => $request->is_defaulter ?? false,
            'goal'          => $request->goal,
        ]);

        return redirect('/students');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'user_id'       => 'sometimes|exists:users,id',
            'instructor_id' => 'nullable|exists:instructors,id',
            'biometric_id'  => 'nullable|string|max:255',
            'rfid_tag'      => 'nullable|string|max:255',
            'birth_date'    => 'nullable|date',
            'is_defaulter'  => 'nullable|boolean',
            'goal'          => 'nullable|in:hypertrophy,weight_loss,conditioning,health,rehabilitation,other',
        ]);

        $student = Student::findOrFail($id);
        $student->update([
            'user_id'       => $request->user_id ?? $student->user_id,
            'instructor_id' => $request->instructor_id ?? $student->instructor_id,
            'biometric_id'  => $request->biometric_id ?? $student->biometric_id,
            'rfid_tag'      => $request->rfid_tag ?? $student->rfid_tag,
            'birth_date'    => $request->birth_date ?? $student->birth_date,
            'is_defaulter'  => $request->has('is_defaulter') ? $request->is_defaulter : $student->is_defaulter,
            'goal'          => $request->goal ?? $student->goal,
        ]);

        return redirect('/students');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}