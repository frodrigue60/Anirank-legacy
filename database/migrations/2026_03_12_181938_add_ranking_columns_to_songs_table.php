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
        Schema::table('songs', function (Blueprint $table) {
            $table->integer('prev_main_rank')->unsigned()->nullable()->after('season_id');
            $table->integer('prev_seasonal_rank')->unsigned()->nullable()->after('prev_main_rank');
            
            $table->index(['prev_main_rank', 'prev_seasonal_rank'], 'idx_songs_ranks');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('songs', function (Blueprint $table) {
            $table->dropIndex('idx_songs_ranks');
            $table->dropColumn(['prev_main_rank', 'prev_seasonal_rank']);
        });
    }
};
