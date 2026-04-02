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
        // Add UUID to main tables
        Schema::table('animes', function (Blueprint $table) {
            if (!Schema::hasColumn('animes', 'uuid')) {
                $table->uuid('uuid')->unique()->nullable()->after('id');
            }
        });

        Schema::table('songs', function (Blueprint $table) {
            if (!Schema::hasColumn('songs', 'uuid')) {
                $table->uuid('uuid')->unique()->nullable()->after('id');
            }
        });

        Schema::table('artists', function (Blueprint $table) {
            if (!Schema::hasColumn('artists', 'uuid')) {
                $table->uuid('uuid')->unique()->nullable()->after('id');
            }
        });

        // Change status in song_reports to boolean
        // Using raw SQL for Postgres type conversion
        DB::statement("ALTER TABLE song_reports ALTER COLUMN status DROP DEFAULT");
        DB::statement("ALTER TABLE song_reports ALTER COLUMN status TYPE boolean USING (status = 'fixed')");
        DB::statement("ALTER TABLE song_reports ALTER COLUMN status SET DEFAULT false");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('animes', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });

        Schema::table('songs', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });

        Schema::table('artists', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });

        DB::statement("ALTER TABLE song_reports ALTER COLUMN status TYPE varchar(255) USING (CASE WHEN status THEN 'fixed' ELSE 'pending' END)");
        DB::statement("ALTER TABLE song_reports ALTER COLUMN status SET DEFAULT 'pending'");
    }
};
