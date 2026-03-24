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
        // 1. Modify daily_metrics table
        DB::statement('ALTER TABLE daily_metrics ALTER COLUMN song_id DROP NOT NULL');
        
        Schema::table('daily_metrics', function (Blueprint $table) {
            if (!Schema::hasColumn('daily_metrics', 'new_users_count')) {
                $table->integer('new_users_count')->default(0);
            }
            if (!Schema::hasColumn('daily_metrics', 'new_ratings_count')) {
                $table->integer('new_ratings_count')->default(0);
            }
            if (!Schema::hasColumn('daily_metrics', 'new_songs_count')) {
                $table->integer('new_songs_count')->default(0);
            }
        });

        // 2. Partial unique index for site-wide metrics (where song_id is null)
        DB::statement('DROP INDEX IF EXISTS daily_metrics_site_wide_unique');
        DB::statement('CREATE UNIQUE INDEX daily_metrics_site_wide_unique ON daily_metrics (date) WHERE song_id IS NULL');

        // 3. Trigger Function for Song Views
        DB::unprepared("
            CREATE OR REPLACE FUNCTION fn_update_daily_song_views() 
            RETURNS TRIGGER AS $$
            BEGIN
                INSERT INTO daily_metrics (song_id, date, views_count, created_at, updated_at)
                VALUES (NEW.id, CURRENT_DATE, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
                ON CONFLICT (song_id, date) 
                DO UPDATE SET 
                    views_count = daily_metrics.views_count + 1,
                    updated_at = CURRENT_TIMESTAMP;
                RETURN NEW;
            END;
            $$ LANGUAGE plpgsql;
        ");

        // 4. Trigger Function for Song Variant Views
        DB::unprepared("
            CREATE OR REPLACE FUNCTION fn_update_daily_variant_views() 
            RETURNS TRIGGER AS $$
            BEGIN
                INSERT INTO daily_metrics (song_id, date, views_count, created_at, updated_at)
                VALUES (NEW.song_id, CURRENT_DATE, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
                ON CONFLICT (song_id, date) 
                DO UPDATE SET 
                    views_count = daily_metrics.views_count + 1,
                    updated_at = CURRENT_TIMESTAMP;
                RETURN NEW;
            END;
            $$ LANGUAGE plpgsql;
        ");

        // 5. Create Triggers
        DB::unprepared("
            DROP TRIGGER IF EXISTS trig_song_views_update ON songs;
            CREATE TRIGGER trig_song_views_update
            AFTER UPDATE OF views ON songs
            FOR EACH ROW
            WHEN (NEW.views > OLD.views)
            EXECUTE FUNCTION fn_update_daily_song_views();
        ");

        DB::unprepared("
            DROP TRIGGER IF EXISTS trig_variant_views_update ON song_variants;
            CREATE TRIGGER trig_variant_views_update
            AFTER UPDATE OF views ON song_variants
            FOR EACH ROW
            WHEN (NEW.views > OLD.views)
            EXECUTE FUNCTION fn_update_daily_variant_views();
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP TRIGGER IF EXISTS trig_song_views_update ON songs;");
        DB::unprepared("DROP TRIGGER IF EXISTS trig_variant_views_update ON song_variants;");
        DB::unprepared("DROP FUNCTION IF EXISTS fn_update_daily_song_views();");
        DB::unprepared("DROP FUNCTION IF EXISTS fn_update_daily_variant_views();");
        DB::unprepared("DROP INDEX IF EXISTS daily_metrics_site_wide_unique;");

        Schema::table('daily_metrics', function (Blueprint $table) {
            $table->dropColumn(['new_users_count', 'new_ratings_count', 'new_songs_count']);
        });

        DB::statement('ALTER TABLE daily_metrics ALTER COLUMN song_id SET NOT NULL');
    }
};
