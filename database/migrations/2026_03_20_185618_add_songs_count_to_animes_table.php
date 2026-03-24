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
        // 1. Add column if not exists
        Schema::table('animes', function (Blueprint $table) {
            if (!Schema::hasColumn('animes', 'songs_count')) {
                $table->integer('songs_count')->default(0);
            }
        });

        // 2. Initial sync
        DB::statement("UPDATE animes a SET songs_count = (SELECT COUNT(*) FROM songs s WHERE s.anime_id = a.id)");

        // 3 & 4. Trigger function and Trigger
        DB::unprepared("
            CREATE OR REPLACE FUNCTION update_anime_songs_count()
            RETURNS TRIGGER AS $$
            BEGIN
                IF (TG_OP = 'INSERT') THEN
                    UPDATE animes SET songs_count = songs_count + 1 WHERE id = NEW.anime_id;
                ELSIF (TG_OP = 'DELETE') THEN
                    UPDATE animes SET songs_count = GREATEST(0, songs_count - 1) WHERE id = OLD.anime_id;
                ELSIF (TG_OP = 'UPDATE' AND (OLD.anime_id IS DISTINCT FROM NEW.anime_id)) THEN
                    IF (OLD.anime_id IS NOT NULL) THEN
                        UPDATE animes SET songs_count = GREATEST(0, songs_count - 1) WHERE id = OLD.anime_id;
                    END IF;
                    IF (NEW.anime_id IS NOT NULL) THEN
                        UPDATE animes SET songs_count = songs_count + 1 WHERE id = NEW.anime_id;
                    END IF;
                END IF;
                RETURN NULL;
            END;
            $$ LANGUAGE plpgsql;

            DROP TRIGGER IF EXISTS trg_update_anime_songs_count ON songs;
            CREATE TRIGGER trg_update_anime_songs_count
            AFTER INSERT OR DELETE OR UPDATE ON songs
            FOR EACH ROW EXECUTE FUNCTION update_anime_songs_count();
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP TRIGGER IF EXISTS trg_update_anime_songs_count ON songs;");
        DB::unprepared("DROP FUNCTION IF EXISTS update_anime_songs_count();");
        
        Schema::table('animes', function (Blueprint $table) {
            $table->dropColumn('songs_count');
        });
    }
};
