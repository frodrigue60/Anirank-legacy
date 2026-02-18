<?php

namespace App\Livewire\Traits;

trait HasRankingScore
{
    private function setScoreSongs($songs, $user = null)
    {
        $format = $user?->score_format ?? 'POINT_100';

        $songs->each(function ($song) use ($user, $format) {
            $song->score     = $song->formattedAvgScore($format);
            $song->userScore = $user ? $song->formattedUserScore($format, $user->id) : null;
        });

        return $songs;
    }
}
