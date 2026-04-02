<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('song_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('song_id')->constrained('songs')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('content')->nullable();
            $table->string('source')->nullable();
            $table->boolean('status')->default(false); // false: pending, true: fixed
            $table->timestamps();
        });

        Schema::create('comment_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('comment_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('content')->nullable();
            $table->string('source')->nullable();
            $table->boolean('status')->default(false); // false: pending, true: resolved
            $table->timestamps();
        });

        Schema::create('user_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reported_user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('reporter_user_id')->constrained('users')->onDelete('cascade');
            $table->string('source', 50)->nullable();
            $table->string('reason', 100);
            $table->text('content');
            $table->boolean('status')->default(false); // false: pending, true: resolved
            $table->timestamps();

            $table->index('reported_user_id');
            $table->index('status');
        });

        Schema::create('user_requests', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('attended_by')->nullable()->constrained('users')->onDelete('cascade');
            $table->boolean('status')->default(false); // false: pending, true: attended
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_requests');
        Schema::dropIfExists('user_reports');
        Schema::dropIfExists('comment_reports');
        Schema::dropIfExists('song_reports');
    }
};
