<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait PublishedScope
{
    /**
     * Boot the trait and apply the global scope.
     */
    public static function bootPublishedScope()
    {
        static::addGlobalScope('published', function (Builder $builder) {
            // Only apply scope to regular users (non-staff)
            /** @var \App\Models\User $user */
            $user = Auth::user() ?? auth('sanctum')->user();
            if ($user && ($user->isAdmin() || $user->isEditor())) {
                return;
            }

            // The model must be active
            $builder->where($builder->getQuery()->from . '.status', 1);

            // Hierarchical checks (Cascade effect)
            $model = $builder->getModel();

            if ($model instanceof \App\Models\Song) {
                // Song must belong to an active Anime
                $builder->whereHas('anime', function ($q) {
                    $q->where('status', 1);
                });
                
                // Song must have at least one active Artist
                $builder->whereHas('artists', function ($q) {
                    $q->where('status', 1);
                });
            }

            if ($model instanceof \App\Models\SongVariant) {
                // Variant must belong to a published Song
                $builder->whereHas('song', function ($q) {
                    $q->where('status', 1);
                    $q->whereHas('anime', function ($sq) {
                        $sq->where('status', 1);
                    });
                    $q->whereHas('artists', function ($sq) {
                        $sq->where('status', 1);
                    });
                });
            }

            if ($model instanceof \App\Models\Video) {
                // Video must belong to a published Variant
                $builder->whereHas('songVariant', function ($q) {
                    $q->where('status', 1);
                    $q->whereHas('song', function ($sq) {
                        $sq->where('status', 1);
                        $sq->whereHas('anime', function ($ssq) {
                            $ssq->where('status', 1);
                        });
                        $sq->whereHas('artists', function ($ssq) {
                            $ssq->where('status', 1);
                        });
                    });
                });
            }
        });
    }

    /**
     * Scope a query to only include published items.
     */
    public function scopePublished(Builder $query)
    {
        return $query->where('status', 1);
    }
}
