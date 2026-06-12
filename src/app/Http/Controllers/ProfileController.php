<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        // Atualizar objetivo do aluno se fornecido
        if ($request->has('goal') || $request->has('custom_goal')) {
            $student = \App\Models\Student::where('user_id', $request->user()->id)->first();
            if ($student) {
                $updateData = [];
                
                if ($request->filled('goal')) {
                    $updateData['goal'] = $request->input('goal');
                }
                
                if ($request->input('goal') === 'other' && $request->filled('custom_goal')) {
                    $updateData['custom_goal'] = $request->input('custom_goal');
                }
                
                if (!empty($updateData)) {
                    $student->update($updateData);
                }
            }
        }

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request)
    {
        $user = $request->user();

        if (!Hash::check((string) $request->input('password'), $user->password)) {
            throw ValidationException::withMessages([
                'password' => __('auth.password'),
            ])->errorBag('userDeletion')->redirectTo(route('profile.edit'));
        }

        $student = \App\Models\Student::where('user_id', $user->id)->first();

        if ($student) {
            \App\Models\Workout::where('student_id', $student->id)->delete();

            $student->delete();
        }

        \Illuminate\Support\Facades\Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
