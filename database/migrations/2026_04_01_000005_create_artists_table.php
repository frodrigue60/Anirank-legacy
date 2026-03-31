<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('artists', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->string('name_jp')->nullable();
            $table->string('slug')->unique();
            $table->boolean('status')->default(false);
            $table->integer('favorites_count')->default(0);
            $table->integer('enabled_songs')->default(0);
            $table->integer('disabled_songs')->default(0);
            $table->unsignedBigInteger('anilist_id')->nullable()->unique();
            $table->string('animethemes_id')->nullable()->unique();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('artists');
    }
};
