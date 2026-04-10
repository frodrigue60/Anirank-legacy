<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('song_variants', function (Blueprint $table) {
            $table->uuid('uuid')->nullable()->after('id');
        });

        // Poblar registros existentes con UUIDs únicos
        DB::table('song_variants')->whereNull('uuid')->orderBy('id')->chunk(100, function ($variants) {
            foreach ($variants as $variant) {
                DB::table('song_variants')
                    ->where('id', $variant->id)
                    ->update(['uuid' => (string) \Illuminate\Support\Str::uuid()]);
            }
        });

        Schema::table('song_variants', function (Blueprint $table) {
            $table->uuid('uuid')->nullable(false)->unique()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('song_variants', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });
    }
};
