<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;
use Symfony\Component\HttpFoundation\Response;

class CheckEnrollment
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->isInstructor() || $user->isManager()) {
            return $next($request);
        }

        $student = Student::where('user_id', $user->id)->first();

        if (!$student || !$student->isEnrolled()) {
            return redirect()->route('enrollment.index')
                ->with('info', 'Você precisa se matricular para acessar esta funcionalidade.');
        }

        return $next($request);
    }
}