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
            $table->boolean('status')->default(false)->after('slug');
        });

        Schema::table('songs', function (Blueprint $table) {
            $table->boolean('status')->default(false)->after('slug');
        });

        Schema::table('song_variants', function (Blueprint $table) {
            $table->boolean('status')->default(false)->after('slug');
        });

        Schema::table('videos', function (Blueprint $table) {
            $table->boolean('status')->default(false)->after('song_variant_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('artists', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('songs', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('song_variants', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('videos', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
