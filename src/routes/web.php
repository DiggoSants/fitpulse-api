<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\ExerciseController;
use App\Http\Controllers\WorkoutController;
use App\Http\Controllers\InstructorController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\RenewalController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\AccessController;
use App\Http\Controllers\FrequencyController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// ── Matrícula ─────────────────────────────────────────────────────────────────
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

// ── Exercícios ────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'verified', 'enrolled'])->group(function () {
    Route::resource('exercises', ExerciseController::class);
});

// ── Treinos ───────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'verified', 'enrolled'])->group(function () {
    Route::resource('workouts', WorkoutController::class)->only([
        'create', 'store', 'edit', 'update', 'destroy'
    ]);
});

// ── Instrutores ───────────────────────────────────────────────────────────────
Route::middleware(['auth', 'verified', 'role:manager'])->group(function () {
    Route::resource('instructors', InstructorController::class);
});

Route::middleware(['auth', 'verified', 'role:manager,instructor'])->group(function () {
    Route::post('/instructors/{id}/regenerate-code', [InstructorController::class, 'regenerateCode'])
        ->name('instructors.regenerate-code');
});

// ── Relatórios (só gerentes) ──────────────────────────────────────────────────
Route::middleware(['auth', 'verified', 'role:manager'])->group(function () {
    Route::get('/reports/plans/comparative',   [ReportController::class, 'plansComparative'])->name('reports.plans.comparative');
    Route::get('/reports/plans/cancellations', [ReportController::class, 'plansCancellations'])->name('reports.plans.cancellations');
    Route::get('/reports/plans/loyalty',       [ReportController::class, 'plansLoyalty'])->name('reports.plans.loyalty');
    Route::get('/reports/users/delinquency',   [ReportController::class, 'usersDelinquency'])->name('reports.users.delinquency'); // ← FALTA ESSA
    Route::get('/reports/frequency/heatmap',   [FrequencyController::class, 'heatmap'])->name('reports.frequency.heatmap');
});

// ── Renovação de planos (ANTES do resource para evitar conflito de rota) ──────
Route::middleware(['auth', 'verified', 'enrolled'])->group(function () {
    Route::get('/plans/renewals', [RenewalController::class, 'history'])->name('plans.renewals');
    Route::post('/plans/renew',   [RenewalController::class, 'renew'])->name('plans.renew');
});

// ── Planos (só gerentes) ──────────────────────────────────────────────────────
Route::middleware(['auth', 'verified', 'role:manager'])->group(function () {
    Route::resource('plans', PlanController::class);
    Route::post('/plans/{id}/restore', [PlanController::class, 'restore'])->name('plans.restore');
});


// ── Mensalidade ───────────────────────────────────────────────────────────────
Route::middleware(['auth', 'verified', 'enrolled'])->group(function () {
    Route::get('/billing',          [BillingController::class, 'index'])->name('billing.index');
    Route::post('/billing/process', [BillingController::class, 'process'])->name('billing.process');
});

Route::middleware(['auth', 'verified', 'role:manager'])->group(function () {
    Route::get('/billing/all', [BillingController::class, 'all'])->name('billing.all');
});

// ── Controle de acesso (só gerentes) ─────────────────────────────────────────
Route::middleware(['auth', 'verified', 'role:manager'])->group(function () {
    Route::get('/access/students',  [AccessController::class, 'students'])->name('access.students');
    Route::post('/access/block',    [AccessController::class, 'block'])->name('access.block');
    Route::post('/access/unblock',  [AccessController::class, 'unblock'])->name('access.unblock');
    Route::post('/access/status',   [AccessController::class, 'updateStatus'])->name('access.status');
});

// ── Frequência ────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'verified', 'enrolled'])->group(function () {
    Route::post('/frequency/register', [FrequencyController::class, 'register'])->name('frequency.register');
});

require __DIR__ . '/auth.php';