<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Backfill the search_index table with all existing records.
     *
     * Each INSERT uses ON CONFLICT DO NOTHING so re-running is safe.
     * The generated search_vector column is populated automatically by PostgreSQL.
     */
    public function up(): void
    {
        // Animes — image field: cover
        DB::unprepared("
            INSERT INTO search_index (item_type, item_id, title, subtitle, slug, image_url)
            SELECT
                'anime',
                uuid,
                title,
                'Anime',
                slug,
                cover
            FROM animes
            WHERE uuid IS NOT NULL
              AND trim(coalesce(title, '')) <> ''
            ON CONFLICT (item_type, item_id) DO NOTHING
        ");

        // Songs — image: none; subtitle carries the theme type (OP, ED, INS…)
        DB::unprepared("
            INSERT INTO search_index (item_type, item_id, title, subtitle, slug, image_url)
            SELECT
                'song',
                uuid,
                coalesce(song_romaji, song_en, song_jp),
                'Song \u2022 ' || type,
                slug,
                NULL
            FROM songs
            WHERE uuid IS NOT NULL
              AND coalesce(song_romaji, song_en, song_jp) IS NOT NULL
            ON CONFLICT (item_type, item_id) DO NOTHING
        ");

        // Artists — image field: avatar
        DB::unprepared("
            INSERT INTO search_index (item_type, item_id, title, subtitle, slug, image_url)
            SELECT
                'artist',
                uuid,
                name,
                'Artist',
                slug,
                avatar
            FROM artists
            WHERE uuid IS NOT NULL
              AND trim(coalesce(name, '')) <> ''
            ON CONFLICT (item_type, item_id) DO NOTHING
        ");

        // Users — image field: avatar
        DB::unprepared("
            INSERT INTO search_index (item_type, item_id, title, subtitle, slug, image_url)
            SELECT
                'user',
                uuid,
                name,
                'User',
                slug,
                avatar
            FROM users
            WHERE uuid IS NOT NULL
              AND trim(coalesce(name, '')) <> ''
            ON CONFLICT (item_type, item_id) DO NOTHING
        ");

        // Studios — image field: logo
        DB::unprepared("
            INSERT INTO search_index (item_type, item_id, title, subtitle, slug, image_url)
            SELECT
                'studio',
                uuid,
                name,
                'Studio',
                slug,
                logo
            FROM studios
            WHERE uuid IS NOT NULL
              AND trim(coalesce(name, '')) <> ''
            ON CONFLICT (item_type, item_id) DO NOTHING
        ");

        // Producers — image field: logo
        DB::unprepared("
            INSERT INTO search_index (item_type, item_id, title, subtitle, slug, image_url)
            SELECT
                'producer',
                uuid,
                name,
                'Producer',
                slug,
                logo
            FROM producers
            WHERE uuid IS NOT NULL
              AND trim(coalesce(name, '')) <> ''
            ON CONFLICT (item_type, item_id) DO NOTHING
        ");
    }

    /**
     * Reverse the migrations.
     *
     * Truncating is safe here because the triggers will repopulate on next
     * INSERT/UPDATE. Alternatively the down() can be a no-op.
     */
    public function down(): void
    {
        DB::unprepared("TRUNCATE TABLE search_index");
    }
};
