<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

use App\Http\Controllers\StudentController;

Route::middleware(['auth'])->group(function () {
    Route::resource('students', StudentController::class);
});
require __DIR__ . '/auth.php';

use App\Http\Controllers\DashboardController;
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth'])
    ->name('dashboard');

use App\Http\Controllers\ExerciseController;

Route::resource('exercises', ExerciseController::class);

use App\Http\Controllers\WorkoutController;

Route::get('/workout/create', [WorkoutController::class,'create']);
Route::post('/workout/store', [WorkoutController::class,'store']);


Route::get('/workout/{id}/edit', [WorkoutController::class, 'edit'])->name('workout.edit');
Route::put('/workout/{id}', [WorkoutController::class, 'update'])->name('workout.update');
Route::get('/workout/create', [WorkoutController::class, 'create'])->name('workout.create');
Route::delete('/workout/{id}', [WorkoutController::class, 'destroy'])->name('workout.destroy');