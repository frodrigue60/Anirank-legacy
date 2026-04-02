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
        Schema::create('user_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reported_user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('reporter_user_id')->constrained('users')->onDelete('cascade');
            $table->string('source', 50)->nullable();
            $table->string('reason');
            $table->text('content')->nullable();
            $table->boolean('status')->default(false); // 0 = Pending, 1 = Resolved
            $table->timestamps();

            $table->index(['reported_user_id', 'status']);
            $table->index('reporter_user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_reports');
    }
};
