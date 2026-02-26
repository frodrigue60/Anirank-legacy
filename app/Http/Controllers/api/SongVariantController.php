<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SongVariant;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class SongVariantController extends Controller
{
    public function index()
    {
        $variants = SongVariant::with('video', 'song.anime')->paginate(18);

        return response()->json([
            'variants' => $variants,
        ]);
    }

    public function setScoreOnlyVariants($variants, $user = null)
    {
        $variants->each(function ($variant) use ($user) {
            $variant->userScore = null;
            $factor = 1;
            $isDecimalFormat = false;
            $denominator = 100;

            if ($user) {
                switch ($user->score_format) {
                    case 'POINT_100':
                        $factor = 1;
                        $denominator = 100;
                        break;
                    case 'POINT_10_DECIMAL':
                        $factor = 0.1;
                        $denominator = 10;
                        $isDecimalFormat = true;
                        break;
                    case 'POINT_10':
                        $factor = 1 / 10;
                        $denominator = 10;
                        break;
                    case 'POINT_5':
                        $factor = 1 / 20;
                        $denominator = 5;
                        $isDecimalFormat = true;
                        break;
                }

                if ($userRating = $this->getUserRating($variant->id, $user->id)) {
                    $variant->userScore = $isDecimalFormat
                        ? round($userRating->rating * $factor, 1)
                        : (int) round($userRating->rating * $factor);
                }
            }

            $variant->score = $isDecimalFormat
                ? round($variant->averageRating * $factor, 1)
                : (int) round($variant->averageRating * $factor);

            $variant->scoreString = $this->formatScoreString(
                $variant->score,
                $user->score_format ?? 'POINT_100',
                $denominator
            );
        });

        return $variants;
    }

    public function sortVariants($sort, $songVariants)
    {
        switch ($sort) {
            case 'title':
                $songVariants = $songVariants->sortBy(function ($song_variant) {
                    return $song_variant->song->anime->title;
                });

                return $songVariants;
                break;

            case 'averageRating':
                $songVariants = $songVariants->sortByDesc('averageRating');

                return $songVariants;
                break;

            case 'view_count':
                $songVariants = $songVariants->sortByDesc('views');

                return $songVariants;
                break;

            case 'likeCount':
                $songVariants = $songVariants->sortByDesc('likeCount');

                return $songVariants;
                break;

            case 'recent':
                $songVariants = $songVariants->sortByDesc('created_at');

                return $songVariants;
                break;

            default:
                $songVariants = $songVariants->sortBy(function ($song_variant) {
                    return $song_variant->song->anime->title;
                });

                return $songVariants;
                break;
        }
    }

    protected function formatScoreString($score, $format, $denominator)
    {
        switch ($format) {
            case 'POINT_100':
                return $score.'/'.$denominator;
            case 'POINT_10_DECIMAL':
                return number_format($score, 1).'/'.$denominator;
            case 'POINT_10':
                return $score.'/'.$denominator;
            case 'POINT_5':
                return number_format($score, 1).'/'.$denominator;
            default:
                return $score.'/'.$denominator;
        }
    }

    public function getUserRating(int $songVariantId, int $userId)
    {
        return DB::table('ratings')
            ->where('rateable_type', SongVariant::class)
            ->where('rateable_id', $songVariantId)
            ->where('user_id', $userId)
            ->first(['rating']);
    }

    public function video(SongVariant $variant)
    {
        $video = $variant->video;
        $video->video_url = Storage::url($video->video_src);

        return response()->json([
            'video' => $video,
        ]);
    }
}
