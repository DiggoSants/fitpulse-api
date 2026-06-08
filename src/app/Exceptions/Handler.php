<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Database\QueryException;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $exception)
    {
        // Se for uma requisição JSON/API
        if ($request->expectsJson()) {
            
            // Erro de autenticação
            if ($exception instanceof AuthenticationException) {
                return response()->json([
                    'message' => 'Sua sessão expirou. Por favor, faça login novamente.',
                    'status' => 'error'
                ], 401);
            }
            
            // Erro de validação
            if ($exception instanceof ValidationException) {
                return response()->json([
                    'message' => 'Dados inválidos. Verifique os campos e tente novamente.',
                    'errors' => $exception->errors(),
                    'status' => 'error'
                ], 422);
            }
            
            // Erro 404 - Página/Recurso não encontrado
            if ($exception instanceof NotFoundHttpException) {
                return response()->json([
                    'message' => 'Recurso não encontrado.',
                    'status' => 'error'
                ], 404);
            }
            
            // Erro de banco de dados
            if ($exception instanceof QueryException) {
                return response()->json([
                    'message' => 'Erro no banco de dados. Tente novamente mais tarde.',
                    'status' => 'error'
                ], 500);
            }
        }
        
        // Para requisições normais (web), deixa o Laravel tratar
        return parent::render($request, $exception);
    }
}