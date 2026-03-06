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
        // 1. Asegurar que existe el formato "TV" (standard de AniList)
        $tvFormatId = DB::table('formats')->where('slug', 'tv')->value('id');

        if (!$tvFormatId) {
            $tvFormatId = DB::table('formats')->insertGetId([
                'name' => 'TV',
                'slug' => 'tv',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 2. Migrar animes de "TV Show" (slug: tv-show) a "TV"
        $tvShowFormat = DB::table('formats')->where('slug', 'tv-show')->first();
        
        if ($tvShowFormat) {
            DB::table('animes')
                ->where('format_id', $tvShowFormat->id)
                ->update(['format_id' => $tvFormatId]);

            // 3. Eliminar el formato obsoleto
            DB::table('formats')->where('id', $tvShowFormat->id)->delete();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No es necesario revertir la eliminación por seguridad de integridad referencial,
        // pero se podría recrear el formato si fuera vital.
    }
};
