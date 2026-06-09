<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;
use App\Models\Enrollment;

class CheckAccess
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // Se não está logado, apenas segue (outros middlewares cuidam da autenticação)
        if (!$user) {
            return $next($request);
        }

        // Verifica se o usuário é um aluno (existe registro na tabela 'students')
        $student = Student::where('user_id', $user->id)->first();
        
        if ($student) {
            // Busca a matrícula que ainda está no prazo (active ou cancelled com end_date futuro)
            $enrollment = Enrollment::where('student_id', $student->id)
                ->where('end_date', '>=', now()->startOfDay())
                ->orderBy('end_date', 'desc')
                ->first();

            $hasAccess = $enrollment && $enrollment->end_date->isFuture();

            if (!$hasAccess) {
                if ($request->expectsJson()) {
                    return response()->json(['message' => 'Seu acesso expirou. Renove sua matrícula.'], 403);
                }
                return redirect()->route('plans')->with('error', 'Seu acesso expirou. Renove para continuar.');
            }
        }

        // Instrutores, gerentes e outros papéis passam sem verificação de acesso
        return $next($request);
    }
}