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
        Schema::table('animes', function (Blueprint $table) {
            $table->integer('enabled_songs')->default(0)->after('slug');
            $table->integer('disabled_songs')->default(0)->after('enabled_songs');
        });

        // 2. Initialize data
        DB::statement("UPDATE animes SET 
            enabled_songs = (SELECT COUNT(*) FROM songs WHERE anime_id = animes.id AND status = true),
            disabled_songs = (SELECT COUNT(*) FROM songs WHERE anime_id = animes.id AND status = false)");

        // 3. Create Hook Function and Trigger
        DB::unprepared("
            CREATE OR REPLACE FUNCTION update_anime_song_counts() RETURNS TRIGGER AS $$
            BEGIN
                IF (TG_OP = 'INSERT') THEN
                    IF NEW.status THEN
                        UPDATE animes SET enabled_songs = enabled_songs + 1 WHERE id = NEW.anime_id;
                    ELSE
                        UPDATE animes SET disabled_songs = disabled_songs + 1 WHERE id = NEW.anime_id;
                    END IF;
                ELSIF (TG_OP = 'UPDATE') THEN
                    -- Case 1: Same anime, status changed
                    IF (OLD.anime_id = NEW.anime_id) AND (OLD.status != NEW.status) THEN
                        IF NEW.status THEN
                            UPDATE animes SET enabled_songs = enabled_songs + 1, disabled_songs = disabled_songs - 1 WHERE id = NEW.anime_id;
                        ELSE
                            UPDATE animes SET enabled_songs = enabled_songs - 1, disabled_songs = disabled_songs + 1 WHERE id = NEW.anime_id;
                        END IF;
                    -- Case 2: Anime changed
                    ELSIF (OLD.anime_id != NEW.anime_id) THEN
                        -- Decrement old anime
                        IF OLD.status THEN
                            UPDATE animes SET enabled_songs = enabled_songs - 1 WHERE id = OLD.anime_id;
                        ELSE
                            UPDATE animes SET disabled_songs = disabled_songs - 1 WHERE id = OLD.anime_id;
                        END IF;
                        -- Increment new anime
                        IF NEW.status THEN
                            UPDATE animes SET enabled_songs = enabled_songs + 1 WHERE id = NEW.anime_id;
                        ELSE
                            UPDATE animes SET disabled_songs = disabled_songs + 1 WHERE id = NEW.anime_id;
                        END IF;
                    END IF;
                ELSIF (TG_OP = 'DELETE') THEN
                    IF OLD.status THEN
                        UPDATE animes SET enabled_songs = enabled_songs - 1 WHERE id = OLD.anime_id;
                    ELSE
                        UPDATE animes SET disabled_songs = disabled_songs - 1 WHERE id = OLD.anime_id;
                    END IF;
                END IF;
                RETURN NULL;
            END;
            $$ LANGUAGE plpgsql;

            CREATE TRIGGER trg_update_anime_song_counts
            AFTER INSERT OR UPDATE OR DELETE ON songs
            FOR EACH ROW EXECUTE FUNCTION update_anime_song_counts();
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP TRIGGER IF EXISTS trg_update_anime_song_counts ON songs");
        DB::unprepared("DROP FUNCTION IF EXISTS update_anime_song_counts()");

        Schema::table('animes', function (Blueprint $table) {
            $table->dropColumn(['enabled_songs', 'disabled_songs']);
        });
    }
};
