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
        //
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
        Student::create([
        'user_id' => $request->user_id,
        'instructor_id' => $request->instructor_id,
        'biometric_id' => $request->biometric_id,
        'rfid_tag' => $request->rfid_tag,
        'birth_date' => $request->birth_date,
        'is_defaulter' => $request->is_defaulter
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
        $student = Student::findOrFail($id);

    $student->update([
        'user_id' => $request->user_id,
        'instructor_id' => $request->instructor_id,
        'biometric_id' => $request->biometric_id,
        'rfid_tag' => $request->rfid_tag,
        'birth_date' => $request->birth_date,
        'is_defaulter' => $request->is_defaulter
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
