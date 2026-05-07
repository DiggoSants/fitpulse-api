<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plan_group_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_group_id')->constrained('plan_groups')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            // Um usuário só pode estar em um grupo por vez
            $table->unique(['plan_group_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plan_group_members');
    }
};