<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            if (!Schema::hasColumn('students', 'goal')) {
                $table->enum('goal', [
                    'hypertrophy',
                    'weight_loss',
                    'conditioning',
                    'health',
                    'rehabilitation',
                    'other'
                ])->nullable()->after('instructor_id');
            }
            
            if (!Schema::hasColumn('students', 'custom_goal')) {
                $table->text('custom_goal')->nullable()->after('goal');
            }
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            if (Schema::hasColumn('students', 'goal')) {
                $table->dropColumn('goal');
            }
            
            if (Schema::hasColumn('students', 'custom_goal')) {
                $table->dropColumn('custom_goal');
            }
        });
    }
};