<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            if (!Schema::hasColumn('students', 'custom_goal')) {
                $column = $table->text('custom_goal')->nullable();

                if (Schema::hasColumn('students', 'goal')) {
                    $column->after('goal');
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            if (Schema::hasColumn('students', 'custom_goal')) {
                $table->dropColumn('custom_goal');
            }
        });
    }
};
