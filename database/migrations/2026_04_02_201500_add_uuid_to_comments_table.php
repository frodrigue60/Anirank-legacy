<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            // Se añade como nullable primero para poder poblar los registros existentes
            $table->uuid('uuid')->nullable()->after('id');
        });

        // Poblar registros existentes con UUIDs únicos
        DB::table('comments')->whereNull('uuid')->orderBy('id')->chunk(500, function ($comments) {
            foreach ($comments as $comment) {
                DB::table('comments')
                    ->where('id', $comment->id)
                    ->update(['uuid' => (string) Str::uuid()]);
            }
        });

        Schema::table('comments', function (Blueprint $table) {
            $table->uuid('uuid')->nullable(false)->unique()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });
    }
};
