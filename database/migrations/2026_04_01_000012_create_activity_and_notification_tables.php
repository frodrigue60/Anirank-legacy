<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('action_type', 50); // 'favorite_song', 'favorite_artist', 'rating', 'comment'
            $table->unsignedBigInteger('target_id');
            $table->string('target_type'); // agnostic: 'song', 'artist', etc.
            $table->text('action_value')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type', 50); // 'reply', 'follow', 'like', etc.
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->string('subject_type', 50)->nullable(); // 'song', 'comment', etc.
            $table->json('data');
            $table->timestamp('read_at')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['user_id', 'read_at', 'created_at'], 'idx_notif_user_unread');
            $table->index(['subject_type', 'subject_id'], 'idx_notif_subject');
        });

        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content')->nullable();
            $table->string('type')->default('info'); // info, success, warning, danger, event
            $table->string('icon')->nullable();
            $table->string('url')->nullable();
            $table->string('image')->nullable();
            $table->integer('priority')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamps();
        });

        Schema::create('follows', function (Blueprint $table) {
            $table->foreignId('follower_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('followed_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->primary(['follower_id', 'followed_id']);
            $table->index('followed_id', 'idx_followed_user');
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('event', 50); // created, updated, deleted
            $table->unsignedBigInteger('auditable_id');
            $table->string('auditable_type', 120);
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->text('url')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('created_at')->nullable();

            $table->index('user_id', 'idx_audit_user');
            $table->index(['auditable_type', 'auditable_id'], 'idx_audit_polymorphic');
            $table->index(['event', 'created_at'], 'idx_audit_event_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('follows');
        Schema::dropIfExists('announcements');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('activities');
    }
};
