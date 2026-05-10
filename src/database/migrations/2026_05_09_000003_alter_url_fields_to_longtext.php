<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Altera campos de URL/imagem de string para longtext.
     * string tem limite de 255 caracteres — URLs longas causam erro.
     */
    public function up(): void
    {
        Schema::table('exercises', function (Blueprint $table) {

            $table->longText('image_url')->nullable()->change();
            $table->longText('video_url')->nullable();
        });

        // products — image
        Schema::table('products', function (Blueprint $table) {
            $table->longText('image')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('exercises', function (Blueprint $table) {

            // volta imagem_url para string
            $table->string('image_url')->nullable()->change();

            // remove video_url
            $table->dropColumn('video_url');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->string('image')->nullable()->change();
        });
    }
};