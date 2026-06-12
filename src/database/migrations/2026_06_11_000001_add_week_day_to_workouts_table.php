<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('workouts', 'week_day')) {
            Schema::table('workouts', function (Blueprint $table) {
                $table->enum('week_day', [
                    'monday',
                    'tuesday',
                    'wednesday',
                    'thursday',
                    'friday',
                    'saturday',
                    'sunday',
                ])->nullable()->after('name');
                $table->index('week_day');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('workouts', 'week_day')) {
            Schema::table('workouts', function (Blueprint $table) {
                $table->dropIndex(['week_day']);
                $table->dropColumn('week_day');
            });
        }
    }
};
