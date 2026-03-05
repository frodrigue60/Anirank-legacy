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
        Schema::table('animes', function (Blueprint $table) {
            $table->string('cover')->nullable();
            $table->string('banner')->nullable();
        });

        Schema::table('artists', function (Blueprint $table) {
            $table->string('avatar')->nullable();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar')->nullable();
            $table->string('banner')->nullable();
        });

        Schema::table('badges', function (Blueprint $table) {
            $table->string('icon')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('animes', function (Blueprint $table) {
            $table->dropColumn(['cover', 'banner']);
        });

        Schema::table('artists', function (Blueprint $table) {
            $table->dropColumn('avatar');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['avatar', 'banner']);
        });

        Schema::table('badges', function (Blueprint $table) {
            $table->dropColumn('icon');
        });
    }
};
