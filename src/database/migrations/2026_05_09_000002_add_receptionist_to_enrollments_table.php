<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adiciona vínculo com o recepcionista responsável pela matrícula.
     */
    public function up(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->foreignId('receptionist_id')
                ->nullable()
                ->after('plan_id')
                ->constrained('receptionists')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropForeign(['receptionist_id']);
            $table->dropColumn('receptionist_id');
        });
    }
};