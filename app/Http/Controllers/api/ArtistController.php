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
    public function index(Request $request)
    {
        $name = $request->name;
        $sort = $request->sort;

        $artists = Artist::when($name, function ($query) use ($name) {
            $query->where('name', 'like', '%'.$name.'%');
        })
            ->withCount('songs')
            ->when($sort === 'name_asc', fn ($q) => $q->orderBy('name'))
            ->when($sort === 'name_desc', fn ($q) => $q->orderByDesc('name'))
            ->when($sort === 'most_themes', fn ($q) => $q->orderByDesc('songs_count'))
            ->when($sort === 'least_themes', fn ($q) => $q->orderBy('songs_count'))
            ->when(!$sort, fn ($q) => $q->orderBy('name'))
            ->with(['images'])
            ->paginate(18);

        $artists->getCollection()->each(function ($artist) {
            $artist->append('avatar_url');
        });

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
        $sort = $request->sort ?? 'recent';
        $name = $request->name;
        $year_id = $request->year_id;
        $season_id = $request->season_id;

        $query = Song::whereHas('artists', function ($query) use ($artist) {
            $query->where('artists.id', $artist->id);
        })->whereHas('post', function ($query) use ($status) {
            $query->where('status', $status);
        });

        if ($year_id) {
            $query->whereHas('post', function($q) use ($year_id) {
                $q->where('year_id', $year_id);
            });
        }
        
        if ($season_id) {
            $query->whereHas('post', function($q) use ($season_id) {
                $q->where('season_id', $season_id);
            });
        }

        if ($name) {
            $query->where(function($q) use ($name) {
                $q->where('title', 'like', '%'.$name.'%')
                  ->orWhereHas('post', function($sq) use ($name) {
                      $sq->where('title', 'like', '%'.$name.'%');
                  });
            });
        }

        if ($type) {
            $query->where('type', $type);
        }

        $query->with(['artists:id,name,slug', 'artists.images', 'post', 'post.images'])
            ->withUserInteractions()
            ->withAvg('ratings', 'rating')
            ->withCount('songVariants as view_count')
            ->withCount('ratings as like_count'); // Approximating popular by rating counts

        // Ordenamos por base de datos antes de paginar
        if ($sort === 'rating') {
            $query->orderBy('ratings_avg_rating', 'desc');
        } elseif ($sort === 'rating_asc') {
            $query->orderBy('ratings_avg_rating', 'asc');
        } elseif ($sort === 'most_views') {
            $query->orderBy('view_count', 'desc');
        } elseif ($sort === 'most_popular') {
            $query->orderBy('like_count', 'desc');
        } elseif ($sort === 'alphabetical') {
            $query->orderBy('title', 'asc');
        } else {
            // recent (default)
            $query->orderBy('created_at', 'desc');
        }

        $songs = $query->paginate(18);
        $songs = $this->setScoreSongs($songs, $user);

        $songs->getCollection()->each(function ($song) {
            if ($song->post) {
                $song->post->append('thumbnail_url');
                $song->post->append('banner_url');
            }
        });

        return response()->json([
            'artist' => $artist,
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
