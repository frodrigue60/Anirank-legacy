<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('animes', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('anilist_id')->nullable()->unique();
            $table->boolean('status')->default(false);
            $table->integer('songs_count')->default(0);
            $table->foreignId('year_id')->nullable()->constrained('years')->onDelete('set null');
            $table->foreignId('season_id')->nullable()->constrained('seasons')->onDelete('set null');
            $table->foreignId('format_id')->nullable()->constrained('formats')->onDelete('set null');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->index('status', 'animes_status_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('animes');
    }
};
