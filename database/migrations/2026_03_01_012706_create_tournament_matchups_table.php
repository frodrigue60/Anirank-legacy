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
        Schema::create('tournament_matchups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tournament_id')->constrained()->cascadeOnDelete();
            $table->integer('round');
            $table->integer('position');
            $table->foreignId('song1_id')->nullable()->constrained('songs')->nullOnDelete();
            $table->foreignId('song2_id')->nullable()->constrained('songs')->nullOnDelete();
            $table->unsignedInteger('song1_votes')->default(0);
            $table->unsignedInteger('song2_votes')->default(0);
            $table->foreignId('winner_song_id')->nullable()->constrained('songs')->nullOnDelete();
            $table->timestamp('ends_at')->nullable();
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tournament_matchups');
    }
};
