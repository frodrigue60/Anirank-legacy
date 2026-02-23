<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Song;
use App\Models\Studio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudioController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $name = $request->name;
        $sort = $request->sort ?? 'most_animes';

        $query = Studio::query()->whereHas('posts', function ($q) {
            $q->where('status', true);
        })->withCount(['posts' => function ($q) {
            $q->where('status', true);
        }]);

        // Load posts to get a banner image (taking the latest one)
        $query->with(['posts' => function ($q) {
            $q->where('status', true)->latest()->with('images');
        }]);

        if ($name) {
            $query->where('name', 'like', '%'.$name.'%');
        }

        switch ($sort) {
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'least_animes':
                $query->orderBy('posts_count', 'asc');
                break;
            case 'most_animes':
            default:
                $query->orderBy('posts_count', 'desc');
                break;
        }

        $studios = $query->paginate(18);

        foreach ($studios as $studio) {
            $featured = $studio->posts->first();
            if ($featured) {
                $featured->append('banner_url');
                $studio->featured_image = $featured->banner_url;
                $studio->featured_title = $featured->title;
            } else {
                $studio->featured_image = null;
                $studio->featured_title = null;
            }
            // Remove the collection to keep the response clean
            unset($studio->posts);
        }

        return response()->json([
            'studios' => $studios,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Studio $studio)
    {
        // $studio->load('posts');

        return response()->json([
            'studio' => $studio,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function animes(Request $request, Studio $studio)
    {
        $status = true;
        $format_id = $request->format_id;
        $name = $request->name;
        $year_id = $request->year_id;
        $season_id = $request->season_id;
        $sort = $request->sort ?? 'title';

        $query = Post::where('status', $status)
            ->whereHas('studios', function ($query) use ($studio) {
                $query->where('studios.id', $studio->id);
            })
            ->with(['format:id,name', 'season:id,name', 'year:id,name', 'images'])
            ->with(['songs' => function ($q) {
                $q->withAvg('ratings', 'rating');
            }])
            ->withCount('songs')
            ->when($name, function ($query) use ($name) {
                $query->where('title', 'like', '%'.$name.'%');
            })
            ->when($year_id, function ($query) use ($year_id) {
                $query->where('year_id', $year_id);
            })
            ->when($season_id, function ($query) use ($season_id) {
                $query->where('season_id', $season_id);
            })
            ->when($format_id, function ($query) use ($format_id) {
                $query->where('format_id', $format_id);
            });

        // Aplicamos el ordenamiento antes de paginar
        if ($sort === 'title') {
            $query->orderBy('title', 'asc');
        } elseif ($sort === 'name_desc') {
            $query->orderBy('title', 'desc');
        } elseif ($sort === 'most_themes') {
            $query->orderBy('songs_count', 'desc');
        } elseif ($sort === 'least_themes') {
            $query->orderBy('songs_count', 'asc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $posts = $query->paginate(18);

        $posts->getCollection()->each(function ($post) {
            $post->append('thumbnail_url');
            $post->average_rating = $post->songs->avg('ratings_avg_rating') ?: 0;
            $post->makeHidden('songs');
        });

        return response()->json([
            'posts' => $posts,
            'studio' => $studio,
        ]);
    }

    public function getUserRating(int $song_id, int $user_id)
    {
        return DB::table('ratings')
            ->where('rateable_type', Song::class)
            ->where('rateable_id', $song_id)
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

    public function sortPosts($sort, $posts)
    {
        switch ($sort) {
            case 'title':

                $posts = $posts->sortBy(function ($post) {
                    return $post->title;
                });

                return $posts;
                break;
            case 'averageRating':
                $posts = $posts->sortByDesc('averageRating');

                return $posts;
            case 'view_count':
                $posts = $posts->sortByDesc('view_count');

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

            // Inizialided attributes
            $song->formattedScore = null;
            $song->rawScore = null;
            $song->scoreString = null;

            $factor = 1;
            $isDecimalFormat = false;
            $denominator = 100; // Por defecto para POINT_100

            if ($user) {
                // Inizialided attributes
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

            // Agregar la propiedad scoreString formateada
            $song->scoreString = $this->formatScoreString(
                $song->formattedScore,
                $user->score_format ?? 'POINT_100',
                $denominator
            );
        });

        return $songs;
    }
}
