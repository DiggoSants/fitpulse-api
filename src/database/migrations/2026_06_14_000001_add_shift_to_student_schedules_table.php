<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('student_schedules', 'shift')) {
            Schema::table('student_schedules', function (Blueprint $table) {
                $table->enum('shift', ['morning', 'afternoon', 'evening', 'full_day'])
                    ->default('full_day')
                    ->after('week_day');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('student_schedules', 'shift')) {
            Schema::table('student_schedules', function (Blueprint $table) {
                $table->dropColumn('shift');
            });
        }
    }
};
