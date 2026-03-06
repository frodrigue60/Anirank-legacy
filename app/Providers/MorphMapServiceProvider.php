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
            'song'         => \App\Models\Song::class,
            'artist'       => \App\Models\Artist::class,
            'song_variant' => \App\Models\SongVariant::class,
            'user'         => \App\Models\User::class,
        ]);
    }
}
