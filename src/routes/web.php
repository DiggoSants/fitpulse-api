<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\ExerciseController;
use App\Http\Controllers\WorkoutController;
use App\Http\Controllers\InstructorController;
use App\Http\Controllers\EnrollmentController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// ── Matrícula (só após login, sem exigir matrícula prévia) ────────────────────
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/enrollment', [EnrollmentController::class, 'index'])->name('enrollment.index');
    Route::post('/enrollment', [EnrollmentController::class, 'store'])->name('enrollment.store');
});

// ── Perfil ────────────────────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ── Alunos ────────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('students', StudentController::class);
});

// ── Exercícios (requer matrícula) ─────────────────────────────────────────────
Route::middleware(['auth', 'verified', 'enrolled'])->group(function () {
    Route::resource('exercises', ExerciseController::class);
});

// ── Treinos (requer matrícula) ────────────────────────────────────────────────
Route::middleware(['auth', 'verified', 'enrolled'])->group(function () {
    Route::resource('workouts', WorkoutController::class)->only([
        'create',
        'store',
        'edit',
        'update',
        'destroy'
    ]);
});

// ── Instrutores (só gerentes) ─────────────────────────────────────────────────
Route::middleware(['auth', 'verified', 'role:manager'])->group(function () {
    Route::resource('instructors', InstructorController::class);
});

Route::middleware(['auth', 'verified', 'role:manager,instructor'])->group(function () {
    Route::post('/instructors/{id}/regenerate-code', [InstructorController::class, 'regenerateCode'])
        ->name('instructors.regenerate-code');
});

// ── Relatórios (só gerentes) ──────────────────────────────────────────────────
use App\Http\Controllers\ReportController;

Route::middleware(['auth', 'verified', 'role:manager'])->group(function () {
    Route::get('/reports/plans/comparative',   [ReportController::class, 'plansComparative'])->name('reports.plans.comparative');
    Route::get('/reports/plans/cancellations', [ReportController::class, 'plansCancellations'])->name('reports.plans.cancellations');
    Route::get('/reports/plans/loyalty',       [ReportController::class, 'plansLoyalty'])->name('reports.plans.loyalty');
});

// ── Planos (só gerentes) ──────────────────────────────────────────────────────
use App\Http\Controllers\PlanController;

Route::middleware(['auth', 'verified', 'role:manager'])->group(function () {
    Route::apiResource('plans', PlanController::class);
    Route::post('/plans/{id}/restore', [PlanController::class, 'restore'])->name('plans.restore');
});

// ── Renovação de planos ───────────────────────────────────────────────────────
use App\Http\Controllers\RenewalController;

Route::middleware(['auth', 'verified', 'enrolled'])->group(function () {
    Route::post('/plans/renew',     [RenewalController::class, 'renew'])->name('plans.renew');
    Route::get('/plans/renewals',   [RenewalController::class, 'history'])->name('plans.renewals');
});

// ── Mensalidade ───────────────────────────────────────────────────────────────
use App\Http\Controllers\BillingController;

// Aluno processa e consulta seus pagamentos
Route::middleware(['auth', 'verified', 'enrolled'])->group(function () {
    Route::post('/billing/process', [BillingController::class, 'process'])->name('billing.process');
    Route::get('/billing',          [BillingController::class, 'index'])->name('billing.index');
});

// Gerente vê todos os pagamentos
Route::middleware(['auth', 'verified', 'role:manager'])->group(function () {
    Route::get('/billing/all', [BillingController::class, 'all'])->name('billing.all');
});

require __DIR__ . '/auth.php';