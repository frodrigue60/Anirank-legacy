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
        // Create the centralized search table with a generated tsvector column.
        // We use DB::unprepared because Blueprint does not support PostgreSQL-specific
        // GENERATED ALWAYS AS (...) STORED columns or tsvector natively.
        DB::unprepared("
            CREATE TABLE IF NOT EXISTS search_index (
                id          BIGSERIAL PRIMARY KEY,
                item_type   VARCHAR(50) NOT NULL,
                item_id     UUID        NOT NULL,
                title       TEXT        NOT NULL,
                subtitle    TEXT,
                slug        TEXT        NOT NULL,
                image_url   TEXT,
                -- Generated column: title gets weight A (most relevant), subtitle gets B
                search_vector tsvector GENERATED ALWAYS AS (
                    setweight(to_tsvector('simple', coalesce(title, '')), 'A') ||
                    setweight(to_tsvector('simple', coalesce(subtitle, '')), 'B')
                ) STORED,
                created_at  TIMESTAMP NULL DEFAULT NOW(),
                updated_at  TIMESTAMP NULL DEFAULT NOW(),
                UNIQUE (item_type, item_id)
            )
        ");

        // GIN index for instant full-text search
        DB::unprepared("
            CREATE INDEX IF NOT EXISTS idx_search_index_vector
                ON search_index USING GIN (search_vector)
        ");

        // Index on item_type to speed up filtering by entity class
        DB::unprepared("
            CREATE INDEX IF NOT EXISTS idx_search_index_type
                ON search_index (item_type)
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP TABLE IF EXISTS search_index");
    }
};
