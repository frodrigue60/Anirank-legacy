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
        Schema::create('ranking_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('song_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('rank');
            $table->integer('seasonal_rank')->nullable();
            $table->decimal('score', 8, 2)->nullable();
            $table->date('date');
            $table->timestamps();

            // Ensure one entry per song per day
            $table->unique(['song_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ranking_histories');
    }
};
