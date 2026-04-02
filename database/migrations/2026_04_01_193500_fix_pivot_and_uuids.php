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
        // Add timestamps to the pivot table for external links
        Schema::table('anime_external_link', function (Blueprint $table) {
            if (!Schema::hasColumn('anime_external_link', 'created_at')) {
                $table->timestamps();
            }
        });

        // Add UUID to main tables
        Schema::table('studios', function (Blueprint $table) {
            if (!Schema::hasColumn('studios', 'uuid')) {
                $table->uuid('uuid')->unique()->nullable()->after('id');
            }
        });

        Schema::table('producers', function (Blueprint $table) {
            if (!Schema::hasColumn('producers', 'uuid')) {
                $table->uuid('uuid')->unique()->nullable()->after('id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('anime_external_link', function (Blueprint $table) {
            $table->dropColumn(['created_at', 'updated_at']);
        });

        Schema::table('studios', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });

        Schema::table('producers', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });
    }
};
