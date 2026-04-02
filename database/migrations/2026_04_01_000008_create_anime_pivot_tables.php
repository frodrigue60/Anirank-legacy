<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('anime_studio', function (Blueprint $table) {
            $table->foreignId('anime_id')->constrained('animes')->onDelete('cascade');
            $table->foreignId('studio_id')->constrained('studios')->onDelete('cascade');
            $table->primary(['anime_id', 'studio_id']);
        });

        Schema::create('anime_producer', function (Blueprint $table) {
            $table->foreignId('anime_id')->constrained('animes')->onDelete('cascade');
            $table->foreignId('producer_id')->constrained('producers')->onDelete('cascade');
            $table->primary(['anime_id', 'producer_id']);
        });

        Schema::create('anime_external_link', function (Blueprint $table) {
            $table->foreignId('anime_id')->constrained('animes')->onDelete('cascade');
            $table->foreignId('external_link_id')->constrained('external_links')->onDelete('cascade');
            $table->primary(['anime_id', 'external_link_id']);
        });

        Schema::create('anime_genre', function (Blueprint $table) {
            $table->foreignId('anime_id')->constrained('animes')->onDelete('cascade');
            $table->foreignId('genre_id')->constrained('genres')->onDelete('cascade');
            $table->primary(['anime_id', 'genre_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anime_genre');
        Schema::dropIfExists('anime_external_link');
        Schema::dropIfExists('anime_producer');
        Schema::dropIfExists('anime_studio');
    }
};
