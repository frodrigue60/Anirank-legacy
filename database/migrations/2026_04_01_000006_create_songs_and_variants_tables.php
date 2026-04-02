<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('songs', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('song_romaji')->nullable();
            $table->string('song_jp')->nullable();
            $table->string('song_en')->nullable();
            $table->string('theme_num')->default(1);
            $table->enum('type', ['OP', 'ED', 'INS', 'OTH'])->default('OP');
            $table->string('slug');
            $table->boolean('status')->default(false);
            $table->unsignedBigInteger('views')->default(0);
            $table->unsignedBigInteger('likes_count')->default(0);
            $table->unsignedBigInteger('dislikes_count')->default(0);
            $table->integer('favorites_count')->default(0);
            $table->decimal('average_score', 5, 2)->default(0.00);
            $table->unsignedInteger('prev_main_rank')->nullable();
            $table->unsignedInteger('prev_seasonal_rank')->nullable();
            $table->string('animethemes_id')->nullable()->unique();
            $table->foreignId('anime_id')->constrained('animes')->onDelete('cascade');
            $table->foreignId('season_id')->constrained('seasons')->onDelete('cascade');
            $table->foreignId('year_id')->constrained('years')->onDelete('cascade');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->index('type', 'songs_type_idx');
            $table->index('views', 'songs_views_idx');
            $table->index('slug', 'songs_slug_idx');
            $table->index('status', 'songs_status_idx');
            $table->index(['prev_main_rank', 'prev_seasonal_rank'], 'idx_songs_ranks');
            $table->unique(['anime_id', 'type', 'theme_num'], 'unique_anime_theme');
        });

        Schema::create('song_variants', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('version_number')->default(1);
            $table->foreignId('song_id')->constrained('songs')->onDelete('cascade');
            $table->unsignedBigInteger('views')->default(0);
            $table->string('slug');
            $table->boolean('spoiler')->default(false);
            $table->boolean('status')->default(false);
            $table->foreignId('season_id')->constrained('seasons')->onDelete('cascade');
            $table->foreignId('year_id')->constrained('years')->onDelete('cascade');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->unique(['song_id', 'version_number'], 'unique_song_version');
        });

        Schema::create('videos', function (Blueprint $table) {
            $table->id();
            $table->string('video_src')->nullable();
            $table->string('embed_code')->nullable();
            $table->string('type')->default('file'); // 'file' | 'embed'
            $table->boolean('status')->default(false);
            $table->foreignId('song_variant_id')->constrained('song_variants')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('artist_song', function (Blueprint $table) {
            $table->foreignId('artist_id')->constrained('artists')->onDelete('cascade');
            $table->foreignId('song_id')->constrained('songs')->onDelete('cascade');
            $table->primary(['artist_id', 'song_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('artist_song');
        Schema::dropIfExists('videos');
        Schema::dropIfExists('song_variants');
        Schema::dropIfExists('songs');
    }
};
