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
        Schema::table('years', function (Blueprint $table) {
            $table->index('current');
            $table->index('name');
        });

        Schema::table('seasons', function (Blueprint $table) {
            $table->index('current');
        });

        Schema::table('genres', function (Blueprint $table) {
            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('years', function (Blueprint $table) {
            $table->dropIndex(['current']);
            $table->dropIndex(['name']);
        });

        Schema::table('seasons', function (Blueprint $table) {
            $table->dropIndex(['current']);
        });

        Schema::table('genres', function (Blueprint $table) {
            $table->dropIndex(['name']);
        });
    }
};
