<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('instructor_change_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('old_instructor_id')->nullable()->constrained('instructors');
            $table->foreignId('new_instructor_id')->constrained('instructors');
            $table->foreignId('changed_by')->constrained('users');
            $table->text('reason')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('instructor_change_logs');
    }
};