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

        // 2. Migrar datos existentes de SongVariant a Song
        DB::table('comments')
            ->where('commentable_type', 'App\\Models\\SongVariant')
            ->join('song_variants', 'comments.commentable_id', '=', 'song_variants.id')
            ->update(['comments.song_id' => DB::raw('song_variants.song_id')]);

        // 3. Manejar respuestas (replicar el song_id del padre)
        // Usamos un loop o una query recursiva si es necesario, pero para niveles simples un update basta
        DB::table('comments as child')
            ->join('comments as parent', 'child.parent_id', '=', 'parent.id')
            ->whereNull('child.song_id')
            ->whereNotNull('parent.song_id')
            ->update(['child.song_id' => DB::raw('parent.song_id')]);

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
