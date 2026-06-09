<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('plans', function (Blueprint $table) {
            if (!Schema::hasColumn('plans', 'is_trial')) {
                $table->boolean('is_trial')->default(false)->after('duration_days');
                $table->unsignedSmallInteger('trial_days')->nullable()->after('is_trial');
                $table->index('is_trial');
            }
        });
    }

    public function down()
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn(['is_trial', 'trial_days']);
        });
    }
};