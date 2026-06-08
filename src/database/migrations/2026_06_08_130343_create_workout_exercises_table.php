<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('workout_exercises')) {
            Schema::create('workout_exercises', function (Blueprint $table) {
                $table->id();
                $table->foreignId('workout_id')->constrained()->onDelete('cascade');
                $table->foreignId('exercise_id')->constrained()->onDelete('cascade');
                $table->integer('sets')->default(3);
                $table->integer('repetitions')->default(10);
                $table->timestamps();
                
                $table->unique(['workout_id', 'exercise_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('workout_exercises');
    }
};