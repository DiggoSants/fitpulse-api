<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plan_renewals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('old_enrollment_id')->constrained('enrollments');
            $table->foreignId('new_enrollment_id')->constrained('enrollments');
            $table->foreignId('plan_id')->constrained();
            $table->timestamp('renewed_at')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plan_renewals');
    }
};