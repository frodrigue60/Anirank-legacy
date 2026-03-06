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
        // 1. Crear tabla score_formats
        Schema::create('score_formats', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('description')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });

        // Seed inicial de score_formats
        DB::table('score_formats')->insert([
            ['name' => '100 Point', 'slug' => 'POINT_100', 'created_at' => now(), 'updated_at' => now()],
            ['name' => '10 Point Decimal', 'slug' => 'POINT_10_DECIMAL', 'created_at' => now(), 'updated_at' => now()],
            ['name' => '10 Point', 'slug' => 'POINT_10', 'created_at' => now(), 'updated_at' => now()],
            ['name' => '5 Star', 'slug' => 'POINT_5', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // 2. Transición de users.score_format (enum) a score_format_id (fk)
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('score_format_id')->nullable()->after('score_format');
        });

        // Migrar datos de score_format a score_format_id
        $formats = DB::table('score_formats')->get();
        foreach ($formats as $format) {
            DB::table('users')
                ->where('score_format', $format->slug)
                ->update(['score_format_id' => $format->id]);
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('score_format');
            $table->foreign('score_format_id')->references('id')->on('score_formats')->onDelete('restrict');
        });

        // 3. Simplificar ratings -> song_ratings
        Schema::rename('ratings', 'song_ratings');
        
        Schema::table('song_ratings', function (Blueprint $table) {
            $table->unsignedBigInteger('song_id')->nullable()->after('id');
        });

        // Migrar rateable_id a song_id (asumiendo que todos son de tipo Song por ahora)
        DB::table('song_ratings')
            ->where('rateable_type', 'App\Models\Song')
            ->update(['song_id' => DB::raw('rateable_id')]);

        Schema::table('song_ratings', function (Blueprint $table) {
            $table->dropColumn(['rateable_type', 'rateable_id']);
            $table->foreign('song_id')->references('id')->on('songs')->onDelete('cascade');
            
            // Reforzar timestamps a nivel DB en song_ratings
            $table->timestamp('created_at')->useCurrent()->change();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->change();
        });

        // 4. Automatizar timestamps en otras tablas clave
        $tables = [
            'animes', 'songs', 'artists', 'comments', 'users', 'formats', 
            'seasons', 'years', 'studios', 'producers', 'genres', 'tags',
            'external_links', 'playlists', 'song_variants'
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->timestamp('created_at')->useCurrent()->change();
                    $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->change();
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revertir estos cambios manualmente sería complejo, 
        // pero básicamente es deshacer los pasos anteriores.
    }
};
