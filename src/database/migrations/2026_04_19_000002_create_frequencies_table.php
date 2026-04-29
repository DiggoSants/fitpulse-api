<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('frequencies')) {
            Schema::create('frequencies', function (Blueprint $table) {
                $table->id();
                $table->foreignId('student_id')->constrained()->cascadeOnDelete();
                $table->timestamps(); // created_at = timestamp da presença
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('frequencies');
    }
};