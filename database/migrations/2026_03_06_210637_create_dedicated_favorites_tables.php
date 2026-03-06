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
        // 1. Crear tabla artist_user
        Schema::create('artist_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('artist_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        // 2. Crear tabla song_user
        Schema::create('song_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('song_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        // 3. Migrar datos de Artist
        DB::table('favorites')
            ->where('favoritable_type', 'App\\Models\\Artist')
            ->orderBy('id')
            ->chunk(100, function ($favorites) {
                foreach ($favorites as $favorite) {
                    DB::table('artist_user')->insert([
                        'artist_id' => $favorite->favoritable_id,
                        'user_id' => $favorite->user_id,
                        'created_at' => $favorite->created_at,
                        'updated_at' => $favorite->updated_at,
                    ]);
                }
            });

        // 4. Migrar datos de Song
        DB::table('favorites')
            ->where('favoritable_type', 'App\\Models\\Song')
            ->orderBy('id')
            ->chunk(100, function ($favorites) {
                foreach ($favorites as $favorite) {
                    DB::table('song_user')->insert([
                        'song_id' => $favorite->favoritable_id,
                        'user_id' => $favorite->user_id,
                        'created_at' => $favorite->created_at,
                        'updated_at' => $favorite->updated_at,
                    ]);
                }
            });

        // 5. Migrar datos de SongVariant a Song
        DB::table('favorites')
            ->where('favoritable_type', 'App\\Models\\SongVariant')
            ->join('song_variants', 'favorites.favoritable_id', '=', 'song_variants.id')
            ->orderBy('favorites.id')
            ->chunk(100, function ($favorites) {
                foreach ($favorites as $favorite) {
                    // Evitar duplicados si ya existe un favorito para el Song base
                    $exists = DB::table('song_user')
                        ->where('song_id', $favorite->song_id)
                        ->where('user_id', $favorite->user_id)
                        ->exists();

                    if (!$exists) {
                        DB::table('song_user')->insert([
                            'song_id' => $favorite->song_id,
                            'user_id' => $favorite->user_id,
                            'created_at' => $favorite->created_at,
                            'updated_at' => $favorite->updated_at,
                        ]);
                    }
                }
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('artist_user');
        Schema::dropIfExists('song_user');
        // The original dedicated_favorites_tables is not created in this migration's up method,
        // so it's not dropped here.
    }
};
