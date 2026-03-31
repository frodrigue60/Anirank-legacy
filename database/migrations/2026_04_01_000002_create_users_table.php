<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->string('slug')->nullable()->unique();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->timestamp('last_login_at')->nullable();

            // Score format (FK to score_formats)
            $table->foreignId('score_format_id')->nullable()->constrained('score_formats')->onDelete('restrict');

            // Profile
            $table->string('about', 500)->nullable();
            $table->string('profile_color', 20)->nullable();
            $table->string('banner')->nullable();

            // Gamification
            $table->unsignedBigInteger('xp')->default(0);
            $table->unsignedInteger('level')->default(1);

            // AniList OAuth
            $table->bigInteger('anilist_id')->unique()->nullable();
            $table->string('anilist_username', 191)->nullable();
            $table->text('anilist_access_token')->nullable();
            $table->text('anilist_refresh_token')->nullable();
            $table->timestamp('anilist_token_expires_at')->nullable();

            // Google OAuth
            $table->string('google_id', 255)->unique()->nullable();
            $table->string('google_email', 255)->unique()->nullable();
            $table->text('google_access_token')->nullable();
            $table->text('google_refresh_token')->nullable();
            $table->timestamp('google_token_expires_at')->nullable();

            $table->rememberToken();
            $table->timestamps();

            // Indexes
            $table->index('google_id', 'idx_users_google_id');
            $table->index('google_email', 'idx_users_google_email');
        });

        Schema::create('password_resets', function (Blueprint $table) {
            $table->string('email')->index();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('personal_access_tokens', function (Blueprint $table) {
            $table->id();
            $table->morphs('tokenable');
            $table->string('name');
            $table->string('token', 64)->unique();
            $table->text('abilities')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('personal_access_tokens');
        Schema::dropIfExists('password_resets');
        Schema::dropIfExists('users');
    }
};
