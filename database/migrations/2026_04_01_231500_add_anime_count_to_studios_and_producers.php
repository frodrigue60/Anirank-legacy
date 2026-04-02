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
        Schema::table('studios', function (Blueprint $table) {
            if (!Schema::hasColumn('studios', 'anime_count')) {
                $table->integer('anime_count')->default(0)->after('logo');
            }
        });

        Schema::table('producers', function (Blueprint $table) {
            if (!Schema::hasColumn('producers', 'anime_count')) {
                $table->integer('anime_count')->default(0)->after('logo');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('studios', function (Blueprint $table) {
            $table->dropColumn('anime_count');
        });

        Schema::table('producers', function (Blueprint $table) {
            $table->dropColumn('anime_count');
        });
    }
};
