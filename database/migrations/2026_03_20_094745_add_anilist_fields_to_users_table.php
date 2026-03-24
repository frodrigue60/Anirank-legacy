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
            $table->bigInteger('anilist_id')->unique()->nullable()->after('banner');
            $table->string('anilist_username', 191)->nullable()->after('anilist_id');
            $table->text('anilist_access_token')->nullable()->after('anilist_username');
            $table->text('anilist_refresh_token')->nullable()->after('anilist_access_token');
            $table->timestamp('anilist_token_expires_at')->nullable()->after('anilist_refresh_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'anilist_id',
                'anilist_username',
                'anilist_access_token',
                'anilist_refresh_token',
                'anilist_token_expires_at',
            ]);
        });
    }
};
