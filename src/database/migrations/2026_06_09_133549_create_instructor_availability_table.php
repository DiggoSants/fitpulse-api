<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('instructor_availability', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instructor_id')->constrained()->onDelete('cascade');
            $table->enum('week_day', [
                'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'
            ]);
            $table->enum('shift', ['morning', 'afternoon', 'evening', 'full_day'])->default('full_day');
            $table->time('start_time')->nullable();  // horário específico (opcional)
            $table->time('end_time')->nullable();    // horário específico (opcional)
            $table->boolean('active')->default(true);
            $table->timestamps();
            
            $table->unique(['instructor_id', 'week_day', 'shift']);
            $table->index(['week_day', 'shift']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('instructor_availability');
    }
};