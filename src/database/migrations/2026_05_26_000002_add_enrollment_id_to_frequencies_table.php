<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('frequencies', function (Blueprint $table) {
            $table->foreignId('enrollment_id')
                ->nullable()
                ->after('student_id')
                ->constrained()
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('frequencies', function (Blueprint $table) {
            $table->dropConstrainedForeignId('enrollment_id');
        });
    }
};
