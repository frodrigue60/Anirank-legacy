<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Drop foreign keys before renaming
        Schema::table('songs', function (Blueprint $table) {
            $table->dropForeign(['post_id']);
        });
        Schema::table('post_studio', function (Blueprint $table) {
            $table->dropForeign(['post_id']);
        });
        Schema::table('external_link_post', function (Blueprint $table) {
            $table->dropForeign(['post_id']);
        });
        Schema::table('post_producer', function (Blueprint $table) {
            $table->dropForeign(['post_id']);
        });
        Schema::table('genre_post', function (Blueprint $table) {
            $table->dropForeign(['post_id']);
        });

        // 2. Rename main table
        Schema::rename('posts', 'animes');

        // 3. Rename FK columns
        Schema::table('songs', function (Blueprint $table) {
            $table->renameColumn('post_id', 'anime_id');
        });
        Schema::table('post_studio', function (Blueprint $table) {
            $table->renameColumn('post_id', 'anime_id');
        });
        Schema::table('external_link_post', function (Blueprint $table) {
            $table->renameColumn('post_id', 'anime_id');
        });
        Schema::table('post_producer', function (Blueprint $table) {
            $table->renameColumn('post_id', 'anime_id');
        });
        Schema::table('genre_post', function (Blueprint $table) {
            $table->renameColumn('post_id', 'anime_id');
        });

        // 4. Rename pivot tables
        Schema::rename('post_studio', 'anime_studio');
        Schema::rename('external_link_post', 'anime_external_link');
        Schema::rename('post_producer', 'anime_producer');
        Schema::rename('genre_post', 'anime_genre');

        // 5. Re-add foreign keys with new names
        Schema::table('songs', function (Blueprint $table) {
            $table->foreign('anime_id')->references('id')->on('animes')->onDelete('cascade');
        });
        Schema::table('anime_studio', function (Blueprint $table) {
            $table->foreign('anime_id')->references('id')->on('animes')->onDelete('cascade');
        });
        Schema::table('anime_external_link', function (Blueprint $table) {
            $table->foreign('anime_id')->references('id')->on('animes')->onDelete('cascade');
        });
        Schema::table('anime_producer', function (Blueprint $table) {
            $table->foreign('anime_id')->references('id')->on('animes')->onDelete('cascade');
        });
        Schema::table('anime_genre', function (Blueprint $table) {
            $table->foreign('anime_id')->references('id')->on('animes')->onDelete('cascade');
        });

        // 6. Update polymorphic imageable_type
        DB::table('images')
            ->where('imageable_type', 'App\\Models\\Post')
            ->update(['imageable_type' => 'App\\Models\\Anime']);
    }

    public function down(): void
    {
        // Update polymorphic types back
        DB::table('images')
            ->where('imageable_type', 'App\\Models\\Anime')
            ->update(['imageable_type' => 'App\\Models\\Post']);

        // Drop new foreign keys
        Schema::table('songs', function (Blueprint $table) {
            $table->dropForeign(['anime_id']);
        });
        Schema::table('anime_studio', function (Blueprint $table) {
            $table->dropForeign(['anime_id']);
        });
        Schema::table('anime_external_link', function (Blueprint $table) {
            $table->dropForeign(['anime_id']);
        });
        Schema::table('anime_producer', function (Blueprint $table) {
            $table->dropForeign(['anime_id']);
        });
        Schema::table('anime_genre', function (Blueprint $table) {
            $table->dropForeign(['anime_id']);
        });

        // Rename pivot tables back
        Schema::rename('anime_studio', 'post_studio');
        Schema::rename('anime_external_link', 'external_link_post');
        Schema::rename('anime_producer', 'post_producer');
        Schema::rename('anime_genre', 'genre_post');

        // Rename FK columns back
        Schema::table('songs', function (Blueprint $table) {
            $table->renameColumn('anime_id', 'post_id');
        });
        Schema::table('post_studio', function (Blueprint $table) {
            $table->renameColumn('anime_id', 'post_id');
        });
        Schema::table('external_link_post', function (Blueprint $table) {
            $table->renameColumn('anime_id', 'post_id');
        });
        Schema::table('post_producer', function (Blueprint $table) {
            $table->renameColumn('anime_id', 'post_id');
        });
        Schema::table('genre_post', function (Blueprint $table) {
            $table->renameColumn('anime_id', 'post_id');
        });

        // Rename main table back
        Schema::rename('animes', 'posts');

        // Re-add old foreign keys
        Schema::table('songs', function (Blueprint $table) {
            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
        });
        Schema::table('post_studio', function (Blueprint $table) {
            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
        });
        Schema::table('external_link_post', function (Blueprint $table) {
            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
        });
        Schema::table('post_producer', function (Blueprint $table) {
            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
        });
        Schema::table('genre_post', function (Blueprint $table) {
            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
        });
    }
};
