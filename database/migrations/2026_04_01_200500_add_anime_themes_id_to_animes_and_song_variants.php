<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('animes', function (Blueprint $table) {
            if (!Schema::hasColumn('animes', 'anime_themes_id')) {
                $table->unsignedBigInteger('anime_themes_id')->nullable()->unique()->after('id');
            }
        });

        Schema::table('song_variants', function (Blueprint $table) {
            if (!Schema::hasColumn('song_variants', 'anime_themes_id')) {
                $table->unsignedBigInteger('anime_themes_id')->nullable()->unique()->after('id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('animes', function (Blueprint $table) {
            $table->dropColumn('anime_themes_id');
        });

        Schema::table('song_variants', function (Blueprint $table) {
            $table->dropColumn('anime_themes_id');
        });
    }
};
