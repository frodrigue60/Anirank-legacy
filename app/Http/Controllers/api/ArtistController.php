<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Artist;
use App\Models\Song;
use App\Models\SongVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ArtistController extends Controller
{
    public function index()
    {
        $artists = Artist::paginate(18);

        return response()->json([
            'artists' => $artists,
        ]);
    }

    public function show(Artist $artist)
    {
        return response()->json([
            'artist' => $artist,
        ]);
    }

    public function songs(Request $request, Artist $artist)
    {
        $user = Auth::check() ? Auth::user() : null;
        $status = true;
        $type = $request->type;
        $sort = $request->sort;
        $name = $request->name;
        $year_id = $request->year_id;
        $season_id = $request->season_id;

        $query = Song::whereHas('artists', function ($query) use ($artist) {
            $query->where('artists.id', $artist->id);
        })
            ->when($year_id, function ($query) use ($year_id) {
                $query->where('year_id', $year_id);
            })
            ->when($season_id, function ($query) use ($season_id) {
                $query->where('season_id', $season_id);
            })
            ->whereHas('post', function ($query) use ($name, $status) {
                $query->where('status', $status)
                    ->when($name, function ($query) use ($name) {
                        $query->where('title', 'like', '%'.$name.'%');
                    });
            })
            ->when($type, function ($query) use ($type) {
                $query->where('type', $type);
            });

        // Ordenamos por base de datos antes de paginar
        if ($sort === 'averageRating') {
            $query->orderByDesc('averageRating');
        } elseif ($sort === 'view_count') {
            $query->orderByDesc('viewCount');
        } elseif ($sort === 'recent') {
            $query->orderByDesc('created_at');
        } else {
            // Unir con posts para ordenar por título si es necesario, o usar orden por defecto
            $query->orderByDesc('id');
        }

        $songs = $query->paginate(18);

        return response()->json([
            'songs' => $songs,
        ]);
    }

    public function getUserRating(int $song_variant_id, int $user_id)
    {
        return DB::table('ratings')
            ->where('rateable_type', SongVariant::class)
            ->where('rateable_id', $song_variant_id)
            ->where('user_id', $user_id)
            ->first(['rating']);
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

    public function sortSongs($sort, $songs)
    {
        switch ($sort) {
            case 'title':
                $songs = $songs->sortBy(function ($song) {
                    return $song->post->title;
                });

                return $songs;
                break;
            case 'averageRating':
                $songs = $songs->sortByDesc('averageRating');

                return $songs;
            case 'view_count':
                $songs = $songs->sortByDesc('view_count');

                return $songs;

            case 'likeCount':
                $songs = $songs->sortByDesc('likeCount');

                return $songs;
                break;
            case 'recent':
                $songs = $songs->sortByDesc('created_at');

                return $songs;
                break;

            default:
                $songs = $songs->sortBy(function ($song) {
                    return $song->post->title;
                });

                return $songs;
                break;
        }
    }

    public function setScoreSongs($songs, $user = null)
    {
        $songs->each(function ($song) use ($user) {
            $song->formattedScore = null;
            $song->rawScore = null;
            $song->scoreString = null;

            $factor = 1;
            $isDecimalFormat = false;
            $denominator = 100;

            if ($user) {
                $song->formattedUserScore = null;
                $song->rawUserScore = null;

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

                if ($userRating = $this->getUserRating($song->id, $user->id)) {
                    $song->formattedUserScore = $isDecimalFormat
                        ? round($userRating->rating * $factor, 1)
                        : (int) round($userRating->rating * $factor);

                    $song->rawUserScore = round($userRating->rating);
                }
            }

            $song->rawScore = round($song->averageRating, 1);

            $song->formattedScore = $isDecimalFormat
                ? round($song->averageRating * $factor, 1)
                : (int) round($song->averageRating * $factor);

            $song->scoreString = $this->formatScoreString(
                $song->formattedScore,
                $user->score_format ?? 'POINT_100',
                $denominator
            );
        });

        return $songs;
    }
}
