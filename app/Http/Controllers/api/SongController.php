<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Favorite;
use App\Models\Reaction;
use App\Models\Season;
use App\Models\Song;
use App\Models\Year;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SongController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $songs = Song::with('post', 'artists', 'likeCount')
            ->paginate(18);

        return response()->json($songs);
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
    public function show($id)
    {
        //
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
    public function update(Request $request, Song $song)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Song $song)
    {
        //
    }

    public function like(Song $song)
    {
        try {
            $this->handleReaction($song, 1);
            $song->updateReactionCounters();

            return response()->json([
                'success' => true,
                'song' => $song,
                'likesCount' => $song->likesCount,
                'dislikesCount' => $song->dislikesCount,
            ]);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'error' => $th]);
        }
    }

    // Método para dislike
    public function dislike(Song $song)
    {
        try {
            $this->handleReaction($song, -1);
            $song->updateReactionCounters();

            return response()->json([
                'success' => true,
                'song' => $song,
                'likesCount' => $song->likesCount,
                'dislikesCount' => $song->dislikesCount,
            ]);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'error' => $th]);
        }
    }

    // Método privado para manejar la reacción
    private function handleReaction($song, $type)
    {
        $user = Auth::check() ? Auth::user() : null;

        // Buscar si ya existe una reacción del usuario para este post
        $reaction = Reaction::where('user_id', $user->id)
            ->where('reactable_id', $song->id)
            ->where('reactable_type', Song::class)
            ->first();

        if ($reaction) {
            if ($reaction->type === $type) {
                // Si la reacción es la misma, eliminarla (toggle)
                $reaction->delete();
            } else {
                // Si la reacción es diferente, actualizarla
                $reaction->update(['type' => $type]);
            }
        } else {
            // Si no existe una reacción, crear una nueva
            Reaction::create([
                'user_id' => $user->id,
                'reactable_id' => $song->id,
                'reactable_type' => Song::class,
                'type' => $type,
            ]);
        }
    }

    public function toggleFavorite(Song $song)
    {

        $user = Auth::check() ? Auth::user() : null;

        // Verificar si el post ya está en favoritos
        $favorite = Favorite::where('user_id', $user->id)
            ->where('favoritable_id', $song->id)
            ->where('favoritable_type', Song::class)
            ->first();

        if ($favorite) {
            $favorite->delete();

            return response()->json([
                'success' => true,
                'message' => 'Removed from favorites',
                'favorite' => false,
            ], 200);
        } else {
            Favorite::create([
                'user_id' => $user->id,
                'favoritable_id' => $song->id,
                'favoritable_type' => Song::class,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Added to favorites',
                'favorite' => true,
            ], 200);
        }
    }

    public function rate(Request $request, Song $song)
    {
        $user = Auth::user();
        $score_format = $user->score_format ?? 'POINT_100';

        $request->validate([
            'score' => 'required|numeric|min:0|max:100',
        ]);

        $score = $request->score;
        $factor = 1;

        switch ($score_format) {
            case 'POINT_5':
                $score = max(20, min(100, ceil($score / 20) * 20));
                $factor = 1 / 20;
                break;
            case 'POINT_10':
                $score = round($score * 10);
                $factor = 1 / 10;
                break;
            case 'POINT_10_DECIMAL':
                $score = round($score * 10, 1);
                $factor = 0.1;
                break;
            case 'POINT_100':
            default:
                $score = round($score);
                $factor = 1;
                break;
        }

        $song->rateOnce($score, $user->id);
        $average = round($song->averageRating * $factor, 1);

        return response()->json([
            'success' => true,
            'message' => 'Rated successfully',
            'score' => $score,
            'average' => $average,
        ]);
    }

    public function seasonal(Request $request)
    {
        $status = true;
        $type = $request->type ?? 'OP';
        $currentSeason = Season::where('current', true)->first();
        $currentYear = Year::where('current', true)->first();
        $sort = 'title';
        if ($currentSeason && $currentYear) {

            $songs = Song::with(['post'])
                ->where('type', $type)
                ->when($currentSeason, function ($query, $currentSeason) {
                    $query->where('season_id', $currentSeason->id);
                })
                ->when($currentYear, function ($query, $currentYear) {
                    $query->where('year_id', $currentYear->id);
                })
                ->whereHas('post', fn ($query) => $query->where('status', $status))
                ->get();

            $songs = $this->sortSongs($sort, $songs);

            return response()->json([
                'success' => true,
                'songs' => $songs,
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'No seasonal songs found',
            ], 404);
        }
    }

    public function globalRanking(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        $status = true;
        $type = $request->type ?? 'OP';
        $limit = 18;

        $songs = Song::where('type', $type)
            ->whereHas('post', fn ($q) => $q->where('status', $status))
            ->with(['post'])
            ->withAvg('ratings', 'rating')
            ->orderByDesc('ratings_avg_rating')
            ->paginate($limit);

        // $songs = $this->setScoreSongs($songs->getCollection(), $user);

        return response()->json([
            'success' => true,
            'songs' => $songs,
        ]);
    }

    public function seasonalRanking(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        $limit = 18;
        $status = true;
        $type = $request->type ?? 'OP';

        $currentSeason = Season::where('current', true)->first();
        $currentYear = Year::where('current', true)->first();

        $songs = Song::where('type', $type)
            ->whereHas('post', function ($query) use ($currentSeason, $currentYear, $status) {
                $query->where('status', $status)
                    ->when($currentSeason, fn ($q) => $q->where('season_id', $currentSeason->id))
                    ->when($currentYear, fn ($q) => $q->where('year_id', $currentYear->id));
            })
            ->with(['post'])
            ->withAvg('ratings', 'rating')
            ->orderByDesc('ratings_avg_rating')
            ->paginate($limit);

        // $songs = $this->setScoreSongs($songs->getCollection(), $user);

        return response()->json([
            'success' => true,
            'songs' => $songs,
            'current_season' => $currentSeason,
            'current_year' => $currentYear,
        ]);
    }

    public function setScoreSongs(Collection|array $songs, $user = null): Collection
    {
        $songs->each(function ($song) use ($user) {
            $song->userScore = null;
            $factor = 1;
            // $isDecimalFormat = false;
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
                        // $isDecimalFormat = true;
                        break;
                    case 'POINT_10':
                        $factor = 1 / 10;
                        $denominator = 10;
                        break;
                    case 'POINT_5':
                        $factor = 1 / 20;
                        $denominator = 5;
                        // $isDecimalFormat = true;
                        break;
                }

                if ($userRating = $this->getUserRating($song->id, $user->id)) {
                    $song->userScore = $isDecimalFormat
                        ? round($userRating->rating * $factor, 1)
                        : (int) round($userRating->rating * $factor);
                }
            }

            $song->score = number_format($song->averageRating * $factor, 1);

            // Agregar la propiedad scoreString formateada
            /* $song->scoreString = $this->formatScoreString(
                $song->score,
                $user->score_format ?? 'POINT_100',
                $denominator
            ); */
        });

        return $songs;
    }

    public function getUserRating(int $song_id, int $user_id)
    {
        return DB::table('ratings')
            ->where('rateable_type', Song::class)
            ->where('rateable_id', $song_id)
            ->where('user_id', $user_id)
            ->first(['rating']);
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

    public function comments(Song $song)
    {
        $comments = Comment::with('replies', 'user')
            ->where('commentable_id', $song->id)
            ->where('commentable_type', Song::class)
            ->where('parent_id', null)
            ->orderByDesc('created_at')
            ->paginate(5);

        return response()->json($comments);
    }
}
