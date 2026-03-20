<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add columns for denormalization
        Schema::table('songs', function (Blueprint $table) {
            $table->integer('favorites_count')->default(0);
            $table->decimal('average_score', 5, 2)->default(0.00);
        });

        Schema::table('artists', function (Blueprint $table) {
            $table->integer('favorites_count')->default(0);
        });

        // 2. Initialize data
        DB::statement("UPDATE songs SET favorites_count = (SELECT COUNT(*) FROM song_user WHERE song_id = songs.id)");
        DB::statement("UPDATE songs SET average_score = COALESCE((SELECT AVG(rating) FROM song_ratings WHERE song_id = songs.id), 0)");
        DB::statement("UPDATE artists SET favorites_count = (SELECT COUNT(*) FROM artist_user WHERE artist_id = artists.id)");

        // 3. Create PostgreSQL Functions and Triggers
        
        // --- Song Favorites ---
        DB::unprepared("
            CREATE OR REPLACE FUNCTION update_song_favorites_count() RETURNS TRIGGER AS $$
            BEGIN
                IF (TG_OP = 'INSERT') THEN
                    UPDATE songs SET favorites_count = favorites_count + 1 WHERE id = NEW.song_id;
                ELSIF (TG_OP = 'DELETE') THEN
                    UPDATE songs SET favorites_count = favorites_count - 1 WHERE id = OLD.song_id;
                END IF;
                RETURN NULL;
            END;
            $$ LANGUAGE plpgsql;

            CREATE TRIGGER trg_update_song_favorites_count
            AFTER INSERT OR DELETE ON song_user
            FOR EACH ROW EXECUTE FUNCTION update_song_favorites_count();
        ");

        // --- Artist Favorites ---
        DB::unprepared("
            CREATE OR REPLACE FUNCTION update_artist_favorites_count() RETURNS TRIGGER AS $$
            BEGIN
                IF (TG_OP = 'INSERT') THEN
                    UPDATE artists SET favorites_count = favorites_count + 1 WHERE id = NEW.artist_id;
                ELSIF (TG_OP = 'DELETE') THEN
                    UPDATE artists SET favorites_count = favorites_count - 1 WHERE id = OLD.artist_id;
                END IF;
                RETURN NULL;
            END;
            $$ LANGUAGE plpgsql;

            CREATE TRIGGER trg_update_artist_favorites_count
            AFTER INSERT OR DELETE ON artist_user
            FOR EACH ROW EXECUTE FUNCTION update_artist_favorites_count();
        ");

        // --- Song Average Score ---
        DB::unprepared("
            CREATE OR REPLACE FUNCTION update_song_average_score() RETURNS TRIGGER AS $$
            BEGIN
                IF (TG_OP = 'INSERT' OR TG_OP = 'UPDATE') THEN
                    UPDATE songs SET average_score = (SELECT AVG(rating) FROM song_ratings WHERE song_id = NEW.song_id) WHERE id = NEW.song_id;
                ELSIF (TG_OP = 'DELETE') THEN
                    UPDATE songs SET average_score = COALESCE((SELECT AVG(rating) FROM song_ratings WHERE song_id = OLD.song_id), 0) WHERE id = OLD.song_id;
                END IF;
                RETURN NULL;
            END;
            $$ LANGUAGE plpgsql;

            CREATE TRIGGER trg_update_song_average_score
            AFTER INSERT OR UPDATE OR DELETE ON song_ratings
            FOR EACH ROW EXECUTE FUNCTION update_song_average_score();
        ");

        // 4. Trigram Indexes for Search
        DB::statement('CREATE EXTENSION IF NOT EXISTS pg_trgm');
        DB::statement('CREATE INDEX animes_title_trgm_idx ON animes USING gin (title gin_trgm_ops)');
        DB::statement('CREATE INDEX songs_romaji_trgm_idx ON songs USING gin (song_romaji gin_trgm_ops)');
        DB::statement('CREATE INDEX songs_en_trgm_idx ON songs USING gin (song_en gin_trgm_ops)');
        DB::statement('CREATE INDEX songs_jp_trgm_idx ON songs USING gin (song_jp gin_trgm_ops)');
        DB::statement('CREATE INDEX artists_name_trgm_idx ON artists USING gin (name gin_trgm_ops)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop Triggers and Functions
        DB::unprepared("DROP TRIGGER IF EXISTS trg_update_song_favorites_count ON song_user");
        DB::unprepared("DROP FUNCTION IF EXISTS update_song_favorites_count()");
        
        DB::unprepared("DROP TRIGGER IF EXISTS trg_update_artist_favorites_count ON artist_user");
        DB::unprepared("DROP FUNCTION IF EXISTS update_artist_favorites_count()");

        DB::unprepared("DROP TRIGGER IF EXISTS trg_update_song_average_score ON song_ratings");
        DB::unprepared("DROP FUNCTION IF EXISTS update_song_average_score()");

        // Remove Trigram Indexes
        Schema::table('animes', function (Blueprint $table) { $table->dropIndex('animes_title_trgm_idx'); });
        Schema::table('songs', function (Blueprint $table) { 
            $table->dropIndex('songs_romaji_trgm_idx');
            $table->dropIndex('songs_en_trgm_idx');
            $table->dropIndex('songs_jp_trgm_idx');
        });
        Schema::table('artists', function (Blueprint $table) { $table->dropIndex('artists_name_trgm_idx'); });

        // Remove columns
        Schema::table('songs', function (Blueprint $table) {
            $table->dropColumn(['favorites_count', 'average_score']);
        });

        Schema::table('artists', function (Blueprint $table) {
            $table->dropColumn('favorites_count');
        });
    }
};
