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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('event', 50); // created, updated, deleted, etc.
            
            // Polymorphic relation
            $table->unsignedBigInteger('auditable_id');
            $table->string('auditable_type', 120);
            
            // Snapshot of changes
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            
            // Metadata
            $table->text('url')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            
            $table->timestamp('created_at')->nullable();

            // Indexes
            $table->index('user_id', 'idx_audit_user');
            $table->index(['auditable_type', 'auditable_id'], 'idx_audit_polymorphic');
            $table->index(['event', 'created_at'], 'idx_audit_event_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
