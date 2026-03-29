<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\ExerciseController;
use App\Http\Controllers\WorkoutController;
use App\Http\Controllers\InstructorController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('students', StudentController::class);
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('exercises', ExerciseController::class);
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('workouts', WorkoutController::class)->only([
        'create',
        'store',
        'edit',
        'update',
        'destroy'
    ]);
});
Route::middleware(['auth', 'verified', 'role:manager'])->group(function () {
    Route::resource('instructors', InstructorController::class);
});

require __DIR__ . '/auth.php';