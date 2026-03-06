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
        Schema::dropIfExists('images');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('images', function (Blueprint $table) {
            $table->id();
            $table->string('path');
            $table->string('type'); // thumbnail, banner, avatar, etc.
            $table->morphs('imageable');
            $table->string('disk')->default('public');
            $table->timestamps();
        });
    }
};
