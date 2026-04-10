<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Update existing slugs for songs in the search index
        DB::unprepared("
            UPDATE search_index si
            SET slug = a.slug || '/' || s.slug
            FROM songs s
            JOIN animes a ON s.anime_id = a.id
            WHERE si.item_id = s.uuid AND si.item_type = 'song';
        ");

        // 2. Update the sync_search_index function to handle future song updates with the nested slug
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
                        -- Fetch the anime slug to create the nested URL format
                        SELECT slug INTO v_slug FROM animes WHERE id = NEW.anime_id;
                        v_slug     := v_slug || '/' || NEW.slug;
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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore the old version of the function (without anime slug for songs)
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

                IF (TG_OP = 'DELETE') THEN
                    DELETE FROM search_index
                     WHERE item_type = v_type
                       AND item_id   = OLD.uuid;
                    RETURN OLD;
                END IF;

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

                IF v_title IS NULL OR trim(v_title) = '' THEN
                    RETURN NEW;
                END IF;

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

        // Restore slugs (remove the anime-slug prefix)
        DB::unprepared("
            UPDATE search_index
            SET slug = split_part(slug, '/', 2)
            WHERE item_type = 'song' AND slug LIKE '%/%';
        ");
    }
};
