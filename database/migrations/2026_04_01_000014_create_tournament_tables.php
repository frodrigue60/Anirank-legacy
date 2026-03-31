<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tournaments', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->integer('size')->default(16);
            $table->enum('status', ['draft', 'active', 'completed'])->default('draft');
            $table->string('type_filter')->nullable(); // 'OP', 'ED', etc.
            $table->integer('current_round')->nullable();
            $table->foreignId('winner_song_id')->nullable()->constrained('songs')->nullOnDelete();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

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

        Schema::create('tournament_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tournament_matchup_id')->constrained('tournament_matchups')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('song_id')->constrained()->cascadeOnDelete();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            $table->unique(['tournament_matchup_id', 'user_id'], 'unique_user_matchup_vote');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tournament_votes');
        Schema::dropIfExists('tournament_matchups');
        Schema::dropIfExists('tournaments');
    }
};
