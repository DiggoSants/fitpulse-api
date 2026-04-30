<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->enum('category', ['suplemento', 'acessorio'])->after('name')->default('suplemento');
            $table->text('description')->nullable()->after('category');
            $table->string('image')->nullable()->after('description');
            $table->decimal('cost', 10, 2)->default(0)->after('price');
            $table->enum('status', ['active', 'inactive'])->default('active')->after('cost');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['category', 'description', 'image', 'cost', 'status']);
        });
    }
};
