<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Creates a single generic PL/pgSQL function and attaches triggers to all
     * searchable tables. The table type ('anime', 'song', etc.) is passed as
     * a trigger argument so we can reuse one function for every model.
     */
    public function up(): void
    {
        // ------------------------------------------------------------------ //
        // 1. Generic sync function — reads TG_ARGV[0] to know the entity type
        // ------------------------------------------------------------------ //
        DB::unprepared("
            CREATE OR REPLACE FUNCTION sync_search_index() RETURNS TRIGGER AS \$\$
            DECLARE
                v_title    TEXT;
                v_subtitle TEXT;
                v_slug     TEXT;
                v_image    TEXT;
                v_type     VARCHAR(50);
            BEGIN
                v_type := TG_ARGV[0];

                -- Handle DELETE: remove the entry from the index
                IF (TG_OP = 'DELETE') THEN
                    DELETE FROM search_index
                     WHERE item_type = v_type
                       AND item_id   = OLD.uuid;
                    RETURN OLD;
                END IF;

                -- Map each table to its searchable fields
                CASE v_type
                    WHEN 'anime' THEN
                        v_title    := NEW.title;
                        v_slug     := NEW.slug;
                        v_image    := NEW.cover;
                        v_subtitle := 'Anime';

                    WHEN 'song' THEN
                        v_title    := coalesce(NEW.song_romaji, NEW.song_en, NEW.song_jp);
                        v_slug     := NEW.slug;
                        v_image    := NULL;
                        v_subtitle := 'Song \u2022 ' || NEW.type;

                    WHEN 'artist' THEN
                        v_title    := NEW.name;
                        v_slug     := NEW.slug;
                        v_image    := NEW.avatar;
                        v_subtitle := 'Artist';

                    WHEN 'user' THEN
                        v_title    := NEW.name;
                        v_slug     := NEW.slug;
                        v_image    := NEW.avatar;
                        v_subtitle := 'User';

                    WHEN 'studio' THEN
                        v_title    := NEW.name;
                        v_slug     := NEW.slug;
                        v_image    := NEW.logo;
                        v_subtitle := 'Studio';

                    WHEN 'producer' THEN
                        v_title    := NEW.name;
                        v_slug     := NEW.slug;
                        v_image    := NEW.logo;
                        v_subtitle := 'Producer';

                    ELSE
                        RETURN NEW;
                END CASE;

                -- Guard: skip if there is no meaningful title to index
                IF v_title IS NULL OR trim(v_title) = '' THEN
                    RETURN NEW;
                END IF;

                -- UPSERT — insert or update on (item_type, item_id) conflict
                INSERT INTO search_index (item_type, item_id, title, subtitle, slug, image_url, updated_at)
                VALUES (v_type, NEW.uuid, v_title, v_subtitle, v_slug, v_image, NOW())
                ON CONFLICT (item_type, item_id) DO UPDATE SET
                    title      = EXCLUDED.title,
                    subtitle   = EXCLUDED.subtitle,
                    slug       = EXCLUDED.slug,
                    image_url  = EXCLUDED.image_url,
                    updated_at = NOW();

                RETURN NEW;
            END;
            \$\$ LANGUAGE plpgsql;
        ");

        // ------------------------------------------------------------------ //
        // 2. Attach triggers to each searchable table
        // ------------------------------------------------------------------ //
        $triggers = [
            'trg_search_anime'     => ['animes',    'anime'],
            'trg_search_song'      => ['songs',     'song'],
            'trg_search_artist'    => ['artists',   'artist'],
            'trg_search_user'      => ['users',     'user'],
            'trg_search_studio'    => ['studios',   'studio'],
            'trg_search_producer'  => ['producers', 'producer'],
        ];

        foreach ($triggers as $triggerName => [$table, $type]) {
            DB::unprepared("
                CREATE TRIGGER {$triggerName}
                AFTER INSERT OR UPDATE OR DELETE ON {$table}
                FOR EACH ROW EXECUTE FUNCTION sync_search_index('{$type}')
            ");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $triggers = [
            'trg_search_anime'    => 'animes',
            'trg_search_song'     => 'songs',
            'trg_search_artist'   => 'artists',
            'trg_search_user'     => 'users',
            'trg_search_studio'   => 'studios',
            'trg_search_producer' => 'producers',
        ];

        foreach ($triggers as $triggerName => $table) {
            DB::unprepared("DROP TRIGGER IF EXISTS {$triggerName} ON {$table}");
        }

        DB::unprepared("DROP FUNCTION IF EXISTS sync_search_index()");
    }
};
