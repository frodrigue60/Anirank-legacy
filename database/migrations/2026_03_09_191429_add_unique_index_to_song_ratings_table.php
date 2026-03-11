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
        Schema::table('song_ratings', function (Blueprint $table) {
            $table->unique(['user_id', 'song_id'], 'ratings_user_song_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('song_ratings', function (Blueprint $table) {
            $table->dropUnique('ratings_user_song_unique');
        });
    }
};
