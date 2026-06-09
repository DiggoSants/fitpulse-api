<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('equipment', function (Blueprint $table) {
            if (!Schema::hasColumn('equipment', 'unique_code')) {
                $table->string('unique_code')->unique()->nullable()->after('id');
            }
            if (!Schema::hasColumn('equipment', 'last_maintenance_date')) {
                $table->date('last_maintenance_date')->nullable()->after('status');
            }
        });
    }

    public function down()
    {
        Schema::table('equipment', function (Blueprint $table) {
            $table->dropColumn(['unique_code', 'last_maintenance_date']);
        });
    }
};