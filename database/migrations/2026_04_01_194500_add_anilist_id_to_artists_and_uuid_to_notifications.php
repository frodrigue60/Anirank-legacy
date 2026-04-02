<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('artists', function (Blueprint $table) {
            if (!Schema::hasColumn('artists', 'anilist_id')) {
                $table->unsignedBigInteger('anilist_id')->nullable()->unique()->after('slug');
            }
        });

        Schema::table('notifications', function (Blueprint $table) {
            if (!Schema::hasColumn('notifications', 'subject_uuid')) {
                $table->uuid('subject_uuid')->nullable()->after('subject_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('artists', function (Blueprint $table) {
            $table->dropColumn('anilist_id');
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->dropColumn('subject_uuid');
        });
    }
};
