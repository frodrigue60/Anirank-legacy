<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('songs', function (Blueprint $table) {
            $table->id();
            $table->string('song_romaji')->nullable();
            $table->string('song_jp')->nullable();
            $table->string('song_en')->nullable();
            $table->string('theme_num')->default(1);
            $table->enum('type', ['OP','ED','INS','OTH'])->default('OP');
            $table->string('slug');
            $table->foreignId('post_id')->references('id')->on('posts')->onDelete('cascade');
            $table->foreignId('season_id')->references('id')->on('seasons')->onDelete('cascade');
            $table->foreignId('year_id')->references('id')->on('years')->onDelete('cascade');
            $table->timestamps();
            $table->unsignedBigInteger('views')->default(0);

            $table->index('type', 'songs_type_idx');
            $table->index('views', 'songs_views_idx');
            $table->index('slug', 'songs_slug_idx');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('songs');
    }
};
