<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class MorphMapServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        \Illuminate\Database\Eloquent\Relations\Relation::morphMap([
            'anime'        => \App\Models\Anime::class,
            'post'         => \App\Models\Anime::class,
            'song'         => \App\Models\Song::class,
            'artist'       => \App\Models\Artist::class,
            'song_variant' => \App\Models\SongVariant::class,
            'user'         => \App\Models\User::class,
            'video'        => \App\Models\Video::class,
            'studio'       => \App\Models\Studio::class,
            'producer'     => \App\Models\Producer::class,
            'genre'        => \App\Models\Genre::class,
            'role'         => \App\Models\Role::class,
            'format'       => \App\Models\Format::class,
            'season'       => \App\Models\Season::class,
            'year'         => \App\Models\Year::class,
        ]);
    }
}
