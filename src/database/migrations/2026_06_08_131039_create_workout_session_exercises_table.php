<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workout_session_exercises', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workout_session_id')->constrained()->onDelete('cascade');
            $table->foreignId('workout_exercise_id')->constrained()->onDelete('cascade');
            $table->boolean('completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->integer('actual_sets')->nullable();
            $table->integer('actual_repetitions')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->unique(['workout_session_id', 'workout_exercise_id'], 'session_exercise_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workout_session_exercises');
    }
};