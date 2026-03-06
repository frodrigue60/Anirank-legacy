<?php

namespace App\Http\Controllers;

use App\Models\Anime;
use App\Models\Artist;
use App\Models\Format;
use App\Models\Season;
use App\Models\Song;
use App\Models\Year;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AnimeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::check() ? Auth::User() : null;
        $status = true;

        $recently = Song::with(['anime:id,title,slug,cover,banner', 'artists:id,name,slug,avatar'])
            ->whereHas('anime', function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->latest()
            ->take(25)
            ->get();

        $popular = Song::with(['anime:id,title,slug,cover,banner', 'artists:id,name,slug,avatar'])
            ->withCount('likes')
            ->whereHas('anime', function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->orderByDesc('likes_count')
            ->take(25)
            ->get();

        $viewed = Song::with(['anime:id,title,slug,cover,banner', 'artists:id,name,slug,avatar'])
            ->whereHas('anime', function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->orderByDesc('views')
            ->take(25)
            ->get();

        $openings = Song::with(['anime:id,title,slug,cover,banner', 'artists:id,name,slug,avatar'])
            ->withAvg('ratings', 'rating')
            ->where('type', 'OP')
            ->whereHas('anime', function ($q) use ($status) {
                $q->where('status', $status);
            })
            ->orderByDesc('ratings_avg_rating')
            ->take(3)
            ->get();

        $endings = Song::with(['anime:id,title,slug,cover,banner', 'artists:id,name,slug,avatar'])
            ->withAvg('ratings', 'rating')
            ->where('type', 'ED')
            ->whereHas('anime', function ($q) use ($status) {
                $q->where('status', $status);
            })
            ->orderByDesc('ratings_avg_rating')
            ->take(3)
            ->get();

        $weaklyRanking = $openings->concat($endings);
        $weaklyRanking = $this->setScoreSongs($weaklyRanking, $user);

        $artists = Artist::select('id', 'name', 'slug', 'avatar')
            ->withCount('songs')
            ->latest()
            ->take(20)
            ->get();

        $featuredSong = Song::with(['anime' => function ($q) {
            $q->select('id', 'title', 'slug', 'cover', 'banner');
        }, 'artists:id,name,slug,avatar'])
            ->whereHas('anime', function ($q) use ($status) {
                $q->where('status', $status);
            })
            ->inRandomOrder()
            ->first();

        return view('index', compact('weaklyRanking', 'recently', 'popular', 'viewed', 'artists', 'featuredSong'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Anime $anime)
    {
        $anime->load(['songs' => function ($q) {
            $q->with(['songVariants.video', 'artists:id,name,slug', 'favorites', 'ratings']);
            $q->withAvg('ratings', 'rating');
        }]);

        $user = Auth::user();

        if (! $anime->status) {
            if ($user && $user->isAdmin()) {
                // Admin can view
            } else {
                return redirect('/')->with('danger', $user ? 'User not autorized!' : 'Anime status: Private');
            }
        }

        $openings = $anime->songs->where('type', 'OP')->sortBy('theme_num');
        $endings = $anime->songs->where('type', 'ED')->sortBy('theme_num');

        return view('public.animes.show', compact('anime', 'openings', 'endings'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Anime $anime) {}

    public function animes(Request $request)
    {
        $seasons = Season::all();
        $years = Year::all()->sortByDesc('name');
        $formats = Format::all();

        return view('public.animes.index', compact('seasons', 'years', 'formats'));
    }

    public function setScoreOnlyVariants($variants, $user = null)
    {
        $variants->each(function ($variant) use ($user) {
            $variant->userScore = null;
            $factor = 1;
            $isDecimalFormat = false;
            $denominator = 100; // Por defecto para POINT_100

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

            // Agregar la propiedad scoreString formateada
            $variant->scoreString = $this->formatScoreString(
                $variant->score,
                $user->score_format ?? 'POINT_100',
                $denominator
            );
        });

        return $variants;
    }

    public function setScoreSongs($songs, $user = null)
    {
        $format = $user?->score_format ?? 'POINT_100';

        $denominatorMap = [
            'POINT_100' => 100,
            'POINT_10_DECIMAL' => 10,
            'POINT_10' => 10,
            'POINT_5' => 5,
        ];
        $denominator = $denominatorMap[$format] ?? 100;

        $songs->each(function ($song) use ($user, $format, $denominator) {
            $song->rawScore = round($song->averageRating, 1);
            $song->formattedScore = $song->formattedAvgScore($format);
            $song->scoreString = $this->formatScoreString($song->formattedScore, $format, $denominator);

            $song->formattedUserScore = null;
            $song->rawUserScore = null;

            if ($user) {
                $userRating = $this->getUserRating($song->id, $user->id);
                if ($userRating) {
                    $song->userFormattedScore = $song->formattedUserScore($format, $user->id);
                    $song->rawUserScore = round($userRating->rating);
                }
            }
        });

        return $songs;
    }

    public function sortSongs($sort, $songs)
    {
        switch ($sort) {
            case 'title':

                $songs = $songs->sortBy(function ($song) {
                    return $song->anime->title;
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
                    return $song->anime->title;
                });

                return $songs;
                break;
        }
    }

    public function sortVariants($sort, $song_variants)
    {
        // dd($song_variants);
        switch ($sort) {
            case 'title':
                $song_variants = $song_variants->sortBy(function ($song_variant) {
                    return $song_variant->song->anime->title;
                });

                return $song_variants;
                break;

            case 'averageRating':
                $song_variants = $song_variants->sortByDesc('averageRating');

                return $song_variants;
                break;

            case 'view_count':
                $song_variants = $song_variants->sortByDesc('views');

                return $song_variants;
                break;

            case 'likeCount':
                $song_variants = $song_variants->sortByDesc('likeCount');

                return $song_variants;
                break;

            case 'recent':
                $song_variants = $song_variants->sortByDesc('created_at');

                return $song_variants;
                break;

            default:
                $song_variants = $song_variants->sortBy(function ($song_variant) {
                    return $song_variant->song->anime->title;
                });

                return $song_variants;
                break;
        }
    }

    public function getUserRating($songId, $userId)
    {
        return DB::table('song_ratings')
            ->where('song_id', $songId)
            ->where('user_id', $userId)
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
}
