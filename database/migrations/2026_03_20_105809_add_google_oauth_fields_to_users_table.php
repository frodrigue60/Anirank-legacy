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
        Schema::table('users', function (Blueprint $table) {
            $table->string('google_id', 255)->unique()->nullable()->after('anilist_token_expires_at');
            $table->string('google_email', 255)->unique()->nullable()->after('google_id');
            $table->text('google_access_token')->nullable()->after('google_email');
            $table->text('google_refresh_token')->nullable()->after('google_access_token');
            $table->timestamp('google_token_expires_at')->nullable()->after('google_refresh_token');

            // Explicit index names as requested
            $table->index('google_id', 'idx_users_google_id');
            $table->index('google_email', 'idx_users_google_email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_users_google_id');
            $table->dropIndex('idx_users_google_email');
            $table->dropColumn([
                'google_id',
                'google_email',
                'google_access_token',
                'google_refresh_token',
                'google_token_expires_at',
            ]);
        });
    }
};
