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
        // 1. Create Handle function to recalculate an artist's stats
        DB::unprepared("
            CREATE OR REPLACE FUNCTION recount_artist_stats(artist_id_param BIGINT) RETURNS VOID AS $$
            BEGIN
                UPDATE artists SET
                    enabled_songs = (
                        SELECT COUNT(*) 
                        FROM artist_song 
                        JOIN songs ON artist_song.song_id = songs.id 
                        WHERE artist_song.artist_id = artist_id_param AND songs.status = true
                    ),
                    disabled_songs = (
                        SELECT COUNT(*) 
                        FROM artist_song 
                        JOIN songs ON artist_song.song_id = songs.id 
                        WHERE artist_song.artist_id = artist_id_param AND songs.status = false
                    )
                WHERE id = artist_id_param;
            END;
            $$ LANGUAGE plpgsql;
        ");

        // 2. Trigger function for artist_song (pivot) table changes
        DB::unprepared("
            CREATE OR REPLACE FUNCTION handle_artist_song_change() RETURNS TRIGGER AS $$
            BEGIN
                IF (TG_OP = 'INSERT' OR TG_OP = 'UPDATE') THEN
                    PERFORM recount_artist_stats(NEW.artist_id);
                END IF;
                IF (TG_OP = 'DELETE' OR TG_OP = 'UPDATE') THEN
                    PERFORM recount_artist_stats(OLD.artist_id);
                END IF;
                RETURN NULL;
            END;
            $$ LANGUAGE plpgsql;

            CREATE TRIGGER trg_recount_artist_on_pivot_change
            AFTER INSERT OR UPDATE OR DELETE ON artist_song
            FOR EACH ROW EXECUTE FUNCTION handle_artist_song_change();
        ");

        // 3. Trigger function for songs table (status) changes
        DB::unprepared("
            CREATE OR REPLACE FUNCTION handle_song_status_change() RETURNS TRIGGER AS $$
            DECLARE
                r RECORD;
            BEGIN
                -- Only trigger if status changed
                IF (OLD.status IS DISTINCT FROM NEW.status) THEN
                    FOR r IN SELECT artist_id FROM artist_song WHERE song_id = NEW.id LOOP
                        PERFORM recount_artist_stats(r.artist_id);
                    END LOOP;
                END IF;
                RETURN NULL;
            END;
            $$ LANGUAGE plpgsql;

            CREATE TRIGGER trg_recount_artists_on_status_change
            AFTER UPDATE OF status ON songs
            FOR EACH ROW EXECUTE FUNCTION handle_song_status_change();
        ");

        // 4. Run initial synchronization for all artists
        DB::statement("
            UPDATE artists SET
                enabled_songs = (
                    SELECT COUNT(*) 
                    FROM artist_song 
                    JOIN songs ON artist_song.song_id = songs.id 
                    WHERE artist_song.artist_id = artists.id AND songs.status = true
                ),
                disabled_songs = (
                    SELECT COUNT(*) 
                    FROM artist_song 
                    JOIN songs ON artist_song.song_id = songs.id 
                    WHERE artist_song.artist_id = artists.id AND songs.status = false
                )
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP TRIGGER IF EXISTS trg_recount_artists_on_status_change ON songs");
        DB::unprepared("DROP FUNCTION IF EXISTS handle_song_status_change()");

        DB::unprepared("DROP TRIGGER IF EXISTS trg_recount_artist_on_pivot_change ON artist_song");
        DB::unprepared("DROP FUNCTION IF EXISTS handle_artist_song_change()");

        DB::unprepared("DROP FUNCTION IF EXISTS recount_artist_stats(BIGINT)");
    }
};
