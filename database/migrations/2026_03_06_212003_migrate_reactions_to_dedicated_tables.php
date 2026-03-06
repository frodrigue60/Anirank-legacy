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
        // 0. Asegurar que DB y Schema estén disponibles
        // (Ya están importados o son fachadas globales en Laravel)

        // 1. Crear nuevas tablas pivote
        Schema::create('song_reactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('song_id')->constrained()->onDelete('cascade');
            $table->tinyInteger('type'); // 1: like, -1: dislike
            $table->timestamps();

            $table->unique(['user_id', 'song_id']);
            $table->index('type');
        });

        Schema::create('comment_reactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('comment_id')->constrained()->onDelete('cascade');
            $table->tinyInteger('type'); // 1: like, -1: dislike
            $table->timestamps();

            $table->unique(['user_id', 'comment_id']);
            $table->index('type');
        });

        // 2. Añadir columnas de conteo a las tablas principales
        Schema::table('songs', function (Blueprint $table) {
            $table->unsignedBigInteger('likes_count')->default(0)->after('slug');
            $table->unsignedBigInteger('dislikes_count')->default(0)->after('likes_count');
        });

        Schema::table('comments', function (Blueprint $table) {
            $table->unsignedBigInteger('likes_count')->default(0)->after('content');
            $table->unsignedBigInteger('dislikes_count')->default(0)->after('likes_count');
        });

        // 3. Migrar datos de reacciones
        // Reacciones de Song
        DB::table('reactions')
            ->where('reactable_type', 'App\\Models\\Song')
            ->get()
            ->each(function ($reaction) {
                DB::table('song_reactions')->insert([
                    'user_id' => $reaction->user_id,
                    'song_id' => $reaction->reactable_id,
                    'type' => $reaction->type,
                    'created_at' => $reaction->created_at,
                    'updated_at' => $reaction->updated_at,
                ]);
            });

        // Reacciones de SongVariant (consolidar en Song)
        DB::table('reactions')
            ->where('reactable_type', 'App\\Models\\SongVariant')
            ->join('song_variants', 'reactions.reactable_id', '=', 'song_variants.id')
            ->select('reactions.*', 'song_variants.song_id')
            ->get()
            ->each(function ($reaction) {
                // Verificar si ya existe una reacción del usuario para esa canción (para evitar duplicados al consolidar)
                $exists = DB::table('song_reactions')
                    ->where('user_id', $reaction->user_id)
                    ->where('song_id', $reaction->song_id)
                    ->exists();

                if (!$exists) {
                    DB::table('song_reactions')->insert([
                        'user_id' => $reaction->user_id,
                        'song_id' => $reaction->song_id,
                        'type' => $reaction->type,
                        'created_at' => $reaction->created_at,
                        'updated_at' => $reaction->updated_at,
                    ]);
                }
            });

        // Reacciones de Comment
        DB::table('reactions')
            ->where('reactable_type', 'App\\Models\\Comment')
            ->get()
            ->each(function ($reaction) {
                DB::table('comment_reactions')->insert([
                    'user_id' => $reaction->user_id,
                    'comment_id' => $reaction->reactable_id,
                    'type' => $reaction->type,
                    'created_at' => $reaction->created_at,
                    'updated_at' => $reaction->updated_at,
                ]);
            });

        // 4. Inicializar contadores
        DB::table('songs')->update([
            'likes_count' => DB::raw("(SELECT COUNT(*) FROM song_reactions WHERE song_id = songs.id AND type = 1)"),
            'dislikes_count' => DB::raw("(SELECT COUNT(*) FROM song_reactions WHERE song_id = songs.id AND type = -1)"),
        ]);

        DB::table('comments')->update([
            'likes_count' => DB::raw("(SELECT COUNT(*) FROM comment_reactions WHERE comment_id = comments.id AND type = 1)"),
            'dislikes_count' => DB::raw("(SELECT COUNT(*) FROM comment_reactions WHERE comment_id = comments.id AND type = -1)"),
        ]);

        // 5. Eliminar tablas obsoletas
        Schema::dropIfExists('reaction_counters');
        Schema::dropIfExists('reactions');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('reactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->morphs('reactable');
            $table->tinyInteger('type')->default(0);
            $table->timestamps();
            $table->index(['user_id', 'reactable_type', 'reactable_id', 'type'], 'reactions_user_reactable_type_idx');
        });

        Schema::create('reaction_counters', function (Blueprint $table) {
            $table->id();
            $table->morphs('reactable');
            $table->unsignedBigInteger('likes_count')->default(0);
            $table->unsignedBigInteger('dislikes_count')->default(0);
            $table->timestamps();
        });

        // Restaurar datos básicos
        DB::table('song_reactions')->get()->each(function ($r) {
            DB::table('reactions')->insert([
                'user_id' => $r->user_id,
                'reactable_id' => $r->song_id,
                'reactable_type' => 'App\\Models\\Song',
                'type' => $r->type,
                'created_at' => $r->created_at,
                'updated_at' => $r->updated_at,
            ]);
        });

        DB::table('comment_reactions')->get()->each(function ($r) {
            DB::table('reactions')->insert([
                'user_id' => $r->user_id,
                'reactable_id' => $r->comment_id,
                'reactable_type' => 'App\\Models\\Comment',
                'type' => $r->type,
                'created_at' => $r->created_at,
                'updated_at' => $r->updated_at,
            ]);
        });

        Schema::dropIfExists('song_reactions');
        Schema::dropIfExists('comment_reactions');

        Schema::table('songs', function (Blueprint $table) {
            $table->dropColumn(['likes_count', 'dislikes_count']);
        });

        Schema::table('comments', function (Blueprint $table) {
            $table->dropColumn(['likes_count', 'dislikes_count']);
        });
    }
};
