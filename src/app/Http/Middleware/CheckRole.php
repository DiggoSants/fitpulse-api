<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
{
    $user = $request->user();

    $allowed = collect($roles)
        ->flatMap(fn($r) => explode(',', $r))
        ->map(fn($r) => trim($r));

    if (!$user || !$allowed->contains($user->role())) {
        abort(403, 'Acesso não autorizado.');
    }

    return $next($request);
}
}