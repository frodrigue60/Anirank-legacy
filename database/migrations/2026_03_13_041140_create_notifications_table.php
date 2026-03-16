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
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type', 50); // e.g., 'reply', 'follow', 'like'

            // Generic Polymorphic Identifiers (Agnostic)
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->string('subject_type', 50)->nullable(); // e.g., 'song', 'comment'

            // Self-contained JSON Payload
            $table->json('data');

            $table->timestamp('read_at')->nullable();
            $table->softDeletes();
            $table->timestamps();

            // Optimal Indexes
            $table->index(['user_id', 'read_at', 'created_at'], 'idx_notif_user_unread');
            $table->index(['subject_type', 'subject_id'], 'idx_notif_subject');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
