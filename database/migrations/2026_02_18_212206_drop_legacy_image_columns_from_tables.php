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
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn(['thumbnail', 'thumbnail_src', 'banner', 'banner_src']);
        });

        Schema::table('artists', function (Blueprint $table) {
            $table->dropColumn(['thumbnail', 'thumbnail_src']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['image', 'banner']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->string('thumbnail')->nullable();
            $table->string('thumbnail_src')->nullable();
            $table->string('banner')->nullable();
            $table->string('banner_src')->nullable();
        });

        Schema::table('artists', function (Blueprint $table) {
            $table->string('thumbnail')->nullable();
            $table->string('thumbnail_src')->nullable();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('image')->nullable();
            $table->string('banner')->nullable();
        });
    }
};
