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
        Schema::create('xp_activities', function (Blueprint $table) {
            $table->id();
            $table->string('key', 50)->unique();
            $table->integer('xp_amount');
            $table->string('description', 255)->nullable();
            $table->integer('cooldown_seconds')->default(0);
            $table->timestamps();
        });

        Schema::create('levels', function (Blueprint $table) {
            $table->unsignedInteger('level')->primary();
            $table->unsignedBigInteger('min_xp')->unique();
            $table->string('name', 50)->nullable();
            $table->unsignedBigInteger('badge_id')->nullable();
            $table->timestamps();

            $table->foreign('badge_id')->references('id')->on('badges')->onDelete('set null');
        });

        Schema::create('xp_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('xp_activity_id')->constrained('xp_activities')->onDelete('cascade');
            $table->integer('xp_amount');
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('xp_logs');
        Schema::dropIfExists('levels');
        Schema::dropIfExists('xp_activities');
    }
};
