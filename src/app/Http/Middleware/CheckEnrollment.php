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

    if ($user->isInstructor() || $user->isManager() || $user->isReceptionist()) {
    return $next($request);
}
    $student = Student::where('user_id', $user->id)->first();

    if (!$student || !$student->isEnrolled()) {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Você precisa ter uma matrícula ativa para usar esta funcionalidade.',
            ], 403);
        }

        return redirect()->route('enrollment.index')
            ->with('info', 'Você precisa se matricular para acessar esta funcionalidade.');
    }

    // Rotas liberadas mesmo bloqueado/devendo
    if ($request->routeIs('billing.*', 'plans.renewals', 'plans.renew', 'access.index')) {
        return $next($request);
    }

    if ($student->isBlocked()) {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Seu acesso está bloqueado. Entre em contato com a academia.',
            ], 403);
        }

        return redirect()->route('access.index')
            ->with('error', 'Seu acesso está bloqueado. Entre em contato com a academia.');
    }

    if ($student->isDelinquent()) {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Seu acesso está suspenso por inadimplência. Regularize seu pagamento.',
            ], 403);
        }

        return redirect()->route('access.index')
            ->with('error', 'Seu acesso está suspenso por inadimplência. Regularize seu pagamento.');
    }

    return $next($request);
}
}
