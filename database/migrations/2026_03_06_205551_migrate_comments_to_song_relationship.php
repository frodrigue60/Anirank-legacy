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
        // 1. Añadir song_id opcional inicialmente
        Schema::table('comments', function (Blueprint $table) {
            $table->foreignId('song_id')->nullable()->after('user_id')->constrained()->onDelete('cascade');
        });

        // 2. Migrar datos existentes de SongVariant a Song (solo si hay datos)
        if (DB::table('comments')->where('commentable_type', 'App\\Models\\SongVariant')->exists()) {
            DB::table('song_variants')->orderBy('id')->chunk(500, function ($variants) {
                foreach ($variants as $variant) {
                    DB::table('comments')
                        ->where('commentable_type', 'App\\Models\\SongVariant')
                        ->where('commentable_id', $variant->id)
                        ->update(['song_id' => $variant->song_id]);
                }
            });
        }

        // 3. Manejar respuestas (replicar el song_id del padre)
        if (DB::table('comments')->whereNotNull('parent_id')->whereNull('song_id')->exists()) {
            // Un simple update con subconsulta para mayor compatibilidad
            DB::table('comments as child')
                ->whereNull('child.song_id')
                ->whereNotNull('child.parent_id')
                ->update([
                    'song_id' => DB::table('comments as parent')
                        ->whereColumn('parent.id', 'child.parent_id')
                        ->select('song_id')
                        ->limit(1)
                ]);
        }

        // 4. Eliminar columnas polimórficas
        Schema::table('comments', function (Blueprint $table) {
            $table->dropColumn(['commentable_id', 'commentable_type']);
            // Hacer song_id obligatorio si se desea, pero lo dejaremos así por ahora para seguridad
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->unsignedBigInteger('commentable_id')->nullable()->after('parent_id');
            $table->string('commentable_type')->nullable()->after('commentable_id');
            $table->dropForeign(['song_id']);
            $table->dropColumn('song_id');
        });

        // Nota: El rollback no restaurará las relaciones polimórficas automáticamente 
        // ya que la información de qué era qué se perdió al borrar las columnas en up().
    }
};
