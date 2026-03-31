<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ─── Infrastructure Tables ───────────────────────────────────────────────

        Schema::create('cache', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->mediumText('value');
            $table->integer('expiration');
        });

        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->text('connection');
            $table->text('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
        });

        // ─── Metrics Tables ──────────────────────────────────────────────────────

        Schema::create('daily_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('song_id')->nullable()->constrained()->onDelete('cascade');
            $table->date('date');
            $table->integer('views_count')->default(0);
            $table->integer('new_users_count')->default(0);
            $table->integer('new_ratings_count')->default(0);
            $table->integer('new_songs_count')->default(0);
            $table->timestamps();

            $table->unique(['song_id', 'date']);
        });

        // Partial unique index for site-wide metrics (song_id IS NULL)
        DB::statement('CREATE UNIQUE INDEX daily_metrics_site_wide_unique ON daily_metrics (date) WHERE song_id IS NULL');

        Schema::create('ranking_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('song_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('rank');
            $table->integer('seasonal_rank')->nullable();
            $table->decimal('score', 8, 2)->nullable();
            $table->date('date');
            $table->timestamps();

            $table->unique(['song_id', 'date']);
        });

        // ─── PostgreSQL Triggers ─────────────────────────────────────────────────

        // 1. Song Favorites Counter
        DB::unprepared("
            CREATE OR REPLACE FUNCTION update_song_favorites_count() RETURNS TRIGGER AS \$\$
            BEGIN
                IF (TG_OP = 'INSERT') THEN
                    UPDATE songs SET favorites_count = favorites_count + 1 WHERE id = NEW.song_id;
                ELSIF (TG_OP = 'DELETE') THEN
                    UPDATE songs SET favorites_count = favorites_count - 1 WHERE id = OLD.song_id;
                END IF;
                RETURN NULL;
            END;
            \$\$ LANGUAGE plpgsql;

            DROP TRIGGER IF EXISTS trg_update_song_favorites_count ON song_user;
            CREATE TRIGGER trg_update_song_favorites_count
            AFTER INSERT OR DELETE ON song_user
            FOR EACH ROW EXECUTE FUNCTION update_song_favorites_count();
        ");

        // 2. Artist Favorites Counter
        DB::unprepared("
            CREATE OR REPLACE FUNCTION update_artist_favorites_count() RETURNS TRIGGER AS \$\$
            BEGIN
                IF (TG_OP = 'INSERT') THEN
                    UPDATE artists SET favorites_count = favorites_count + 1 WHERE id = NEW.artist_id;
                ELSIF (TG_OP = 'DELETE') THEN
                    UPDATE artists SET favorites_count = favorites_count - 1 WHERE id = OLD.artist_id;
                END IF;
                RETURN NULL;
            END;
            \$\$ LANGUAGE plpgsql;

            DROP TRIGGER IF EXISTS trg_update_artist_favorites_count ON artist_user;
            CREATE TRIGGER trg_update_artist_favorites_count
            AFTER INSERT OR DELETE ON artist_user
            FOR EACH ROW EXECUTE FUNCTION update_artist_favorites_count();
        ");

        // 3. Song Average Score
        DB::unprepared("
            CREATE OR REPLACE FUNCTION update_song_average_score() RETURNS TRIGGER AS \$\$
            BEGIN
                IF (TG_OP = 'INSERT' OR TG_OP = 'UPDATE') THEN
                    UPDATE songs SET average_score = (SELECT AVG(rating) FROM song_ratings WHERE song_id = NEW.song_id) WHERE id = NEW.song_id;
                ELSIF (TG_OP = 'DELETE') THEN
                    UPDATE songs SET average_score = COALESCE((SELECT AVG(rating) FROM song_ratings WHERE song_id = OLD.song_id), 0) WHERE id = OLD.song_id;
                END IF;
                RETURN NULL;
            END;
            \$\$ LANGUAGE plpgsql;

            DROP TRIGGER IF EXISTS trg_update_song_average_score ON song_ratings;
            CREATE TRIGGER trg_update_song_average_score
            AFTER INSERT OR UPDATE OR DELETE ON song_ratings
            FOR EACH ROW EXECUTE FUNCTION update_song_average_score();
        ");

        // 4. Anime Songs Count
        DB::unprepared("
            CREATE OR REPLACE FUNCTION update_anime_songs_count()
            RETURNS TRIGGER AS \$\$
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
            \$\$ LANGUAGE plpgsql;

            DROP TRIGGER IF EXISTS trg_update_anime_songs_count ON songs;
            CREATE TRIGGER trg_update_anime_songs_count
            AFTER INSERT OR DELETE OR UPDATE ON songs
            FOR EACH ROW EXECUTE FUNCTION update_anime_songs_count();
        ");

        // 5. Studio Anime Count
        DB::unprepared("
            CREATE OR REPLACE FUNCTION fn_update_studio_anime_count()
            RETURNS TRIGGER AS \$\$
            BEGIN
                IF (TG_OP = 'INSERT') THEN
                    UPDATE studios SET anime_count = anime_count + 1 WHERE id = NEW.studio_id;
                ELSIF (TG_OP = 'DELETE') THEN
                    UPDATE studios SET anime_count = GREATEST(0, anime_count - 1) WHERE id = OLD.studio_id;
                END IF;
                RETURN NULL;
            END;
            \$\$ LANGUAGE plpgsql;

            DROP TRIGGER IF EXISTS trg_update_studio_anime_count ON anime_studio;
            CREATE TRIGGER trg_update_studio_anime_count
            AFTER INSERT OR DELETE ON anime_studio
            FOR EACH ROW EXECUTE FUNCTION fn_update_studio_anime_count();
        ");

        // 6. Producer Anime Count
        DB::unprepared("
            CREATE OR REPLACE FUNCTION fn_update_producer_anime_count()
            RETURNS TRIGGER AS \$\$
            BEGIN
                IF (TG_OP = 'INSERT') THEN
                    UPDATE producers SET anime_count = anime_count + 1 WHERE id = NEW.producer_id;
                ELSIF (TG_OP = 'DELETE') THEN
                    UPDATE producers SET anime_count = GREATEST(0, anime_count - 1) WHERE id = OLD.producer_id;
                END IF;
                RETURN NULL;
            END;
            \$\$ LANGUAGE plpgsql;

            DROP TRIGGER IF EXISTS trg_update_producer_anime_count ON anime_producer;
            CREATE TRIGGER trg_update_producer_anime_count
            AFTER INSERT OR DELETE ON anime_producer
            FOR EACH ROW EXECUTE FUNCTION fn_update_producer_anime_count();
        ");

        // 7. Artist Song Counters (pivot + status change + deletion)
        DB::unprepared("
            CREATE OR REPLACE FUNCTION fn_update_artist_song_counters_deletion()
            RETURNS TRIGGER AS \$\$
            BEGIN
                IF (OLD.status = TRUE) THEN
                    UPDATE artists SET enabled_songs = GREATEST(0, enabled_songs - 1)
                    WHERE id IN (SELECT artist_id FROM artist_song WHERE song_id = OLD.id);
                ELSE
                    UPDATE artists SET disabled_songs = GREATEST(0, disabled_songs - 1)
                    WHERE id IN (SELECT artist_id FROM artist_song WHERE song_id = OLD.id);
                END IF;
                RETURN OLD;
            END;
            \$\$ LANGUAGE plpgsql;

            DROP TRIGGER IF EXISTS trg_artist_song_counters_deletion ON songs;
            CREATE TRIGGER trg_artist_song_counters_deletion
            BEFORE DELETE ON songs
            FOR EACH ROW EXECUTE FUNCTION fn_update_artist_song_counters_deletion();

            CREATE OR REPLACE FUNCTION fn_update_artist_song_counters_pivot()
            RETURNS TRIGGER AS \$\$
            DECLARE
                song_status BOOLEAN;
            BEGIN
                IF (TG_OP = 'INSERT') THEN
                    SELECT status INTO song_status FROM songs WHERE id = NEW.song_id;
                    IF (song_status = TRUE) THEN
                        UPDATE artists SET enabled_songs = enabled_songs + 1 WHERE id = NEW.artist_id;
                    ELSE
                        UPDATE artists SET disabled_songs = disabled_songs + 1 WHERE id = NEW.artist_id;
                    END IF;
                ELSIF (TG_OP = 'DELETE') THEN
                    SELECT status INTO song_status FROM songs WHERE id = OLD.song_id;
                    IF (song_status IS NOT NULL) THEN
                        IF (song_status = TRUE) THEN
                            UPDATE artists SET enabled_songs = GREATEST(0, enabled_songs - 1) WHERE id = OLD.artist_id;
                        ELSE
                            UPDATE artists SET disabled_songs = GREATEST(0, disabled_songs - 1) WHERE id = OLD.artist_id;
                        END IF;
                    END IF;
                END IF;
                RETURN NULL;
            END;
            \$\$ LANGUAGE plpgsql;

            DROP TRIGGER IF EXISTS trg_artist_song_counters_pivot ON artist_song;
            CREATE TRIGGER trg_artist_song_counters_pivot
            AFTER INSERT OR DELETE ON artist_song
            FOR EACH ROW EXECUTE FUNCTION fn_update_artist_song_counters_pivot();

            CREATE OR REPLACE FUNCTION fn_update_artist_song_counters_status()
            RETURNS TRIGGER AS \$\$
            BEGIN
                IF (OLD.status = FALSE AND NEW.status = TRUE) THEN
                    UPDATE artists SET
                        enabled_songs = enabled_songs + 1,
                        disabled_songs = GREATEST(0, disabled_songs - 1)
                    WHERE id IN (SELECT artist_id FROM artist_song WHERE song_id = NEW.id);
                ELSIF (OLD.status = TRUE AND NEW.status = FALSE) THEN
                    UPDATE artists SET
                        enabled_songs = GREATEST(0, enabled_songs - 1),
                        disabled_songs = disabled_songs + 1
                    WHERE id IN (SELECT artist_id FROM artist_song WHERE song_id = NEW.id);
                END IF;
                RETURN NULL;
            END;
            \$\$ LANGUAGE plpgsql;

            DROP TRIGGER IF EXISTS trg_artist_song_counters_status ON songs;
            CREATE TRIGGER trg_artist_song_counters_status
            AFTER UPDATE OF status ON songs
            FOR EACH ROW EXECUTE FUNCTION fn_update_artist_song_counters_status();
        ");

        // 8. Daily Metrics: Song Views
        DB::unprepared("
            CREATE OR REPLACE FUNCTION fn_update_daily_song_views()
            RETURNS TRIGGER AS \$\$
            BEGIN
                INSERT INTO daily_metrics (song_id, date, views_count, created_at, updated_at)
                VALUES (NEW.id, CURRENT_DATE, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
                ON CONFLICT (song_id, date)
                DO UPDATE SET
                    views_count = daily_metrics.views_count + 1,
                    updated_at = CURRENT_TIMESTAMP;
                RETURN NEW;
            END;
            \$\$ LANGUAGE plpgsql;

            DROP TRIGGER IF EXISTS trig_song_views_update ON songs;
            CREATE TRIGGER trig_song_views_update
            AFTER UPDATE OF views ON songs
            FOR EACH ROW
            WHEN (NEW.views > OLD.views)
            EXECUTE FUNCTION fn_update_daily_song_views();
        ");

        // 9. Daily Metrics: Song Variant Views
        DB::unprepared("
            CREATE OR REPLACE FUNCTION fn_update_daily_variant_views()
            RETURNS TRIGGER AS \$\$
            BEGIN
                INSERT INTO daily_metrics (song_id, date, views_count, created_at, updated_at)
                VALUES (NEW.song_id, CURRENT_DATE, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
                ON CONFLICT (song_id, date)
                DO UPDATE SET
                    views_count = daily_metrics.views_count + 1,
                    updated_at = CURRENT_TIMESTAMP;
                RETURN NEW;
            END;
            \$\$ LANGUAGE plpgsql;

            DROP TRIGGER IF EXISTS trig_variant_views_update ON song_variants;
            CREATE TRIGGER trig_variant_views_update
            AFTER UPDATE OF views ON song_variants
            FOR EACH ROW
            WHEN (NEW.views > OLD.views)
            EXECUTE FUNCTION fn_update_daily_variant_views();
        ");

        // 10. Trigram Search Indexes
        DB::statement('CREATE EXTENSION IF NOT EXISTS pg_trgm');
        DB::statement('CREATE INDEX IF NOT EXISTS animes_title_trgm_idx ON animes USING gin (title gin_trgm_ops)');
        DB::statement('CREATE INDEX IF NOT EXISTS songs_romaji_trgm_idx ON songs USING gin (song_romaji gin_trgm_ops)');
        DB::statement('CREATE INDEX IF NOT EXISTS songs_en_trgm_idx ON songs USING gin (song_en gin_trgm_ops)');
        DB::statement('CREATE INDEX IF NOT EXISTS songs_jp_trgm_idx ON songs USING gin (song_jp gin_trgm_ops)');
        DB::statement('CREATE INDEX IF NOT EXISTS artists_name_trgm_idx ON artists USING gin (name gin_trgm_ops)');
    }

    public function down(): void
    {
        // Drop Triggers and Functions
        DB::unprepared("DROP TRIGGER IF EXISTS trig_variant_views_update ON song_variants");
        DB::unprepared("DROP TRIGGER IF EXISTS trig_song_views_update ON songs");
        DB::unprepared("DROP FUNCTION IF EXISTS fn_update_daily_variant_views()");
        DB::unprepared("DROP FUNCTION IF EXISTS fn_update_daily_song_views()");

        DB::unprepared("DROP TRIGGER IF EXISTS trg_artist_song_counters_status ON songs");
        DB::unprepared("DROP TRIGGER IF EXISTS trg_artist_song_counters_pivot ON artist_song");
        DB::unprepared("DROP TRIGGER IF EXISTS trg_artist_song_counters_deletion ON songs");
        DB::unprepared("DROP FUNCTION IF EXISTS fn_update_artist_song_counters_status()");
        DB::unprepared("DROP FUNCTION IF EXISTS fn_update_artist_song_counters_pivot()");
        DB::unprepared("DROP FUNCTION IF EXISTS fn_update_artist_song_counters_deletion()");

        DB::unprepared("DROP TRIGGER IF EXISTS trg_update_producer_anime_count ON anime_producer");
        DB::unprepared("DROP FUNCTION IF EXISTS fn_update_producer_anime_count()");

        DB::unprepared("DROP TRIGGER IF EXISTS trg_update_studio_anime_count ON anime_studio");
        DB::unprepared("DROP FUNCTION IF EXISTS fn_update_studio_anime_count()");

        DB::unprepared("DROP TRIGGER IF EXISTS trg_update_anime_songs_count ON songs");
        DB::unprepared("DROP FUNCTION IF EXISTS update_anime_songs_count()");

        DB::unprepared("DROP TRIGGER IF EXISTS trg_update_song_average_score ON song_ratings");
        DB::unprepared("DROP FUNCTION IF EXISTS update_song_average_score()");

        DB::unprepared("DROP TRIGGER IF EXISTS trg_update_artist_favorites_count ON artist_user");
        DB::unprepared("DROP FUNCTION IF EXISTS update_artist_favorites_count()");

        DB::unprepared("DROP TRIGGER IF EXISTS trg_update_song_favorites_count ON song_user");
        DB::unprepared("DROP FUNCTION IF EXISTS update_song_favorites_count()");

        // Drop Trigram Indexes
        DB::unprepared("DROP INDEX IF EXISTS artists_name_trgm_idx");
        DB::unprepared("DROP INDEX IF EXISTS songs_jp_trgm_idx");
        DB::unprepared("DROP INDEX IF EXISTS songs_en_trgm_idx");
        DB::unprepared("DROP INDEX IF EXISTS songs_romaji_trgm_idx");
        DB::unprepared("DROP INDEX IF EXISTS animes_title_trgm_idx");
        DB::unprepared("DROP INDEX IF EXISTS daily_metrics_site_wide_unique");

        Schema::dropIfExists('ranking_histories');
        Schema::dropIfExists('daily_metrics');
        Schema::dropIfExists('failed_jobs');
        Schema::dropIfExists('cache');
    }
};
