<?php

namespace App\Http\Controllers;

use App\Models\Anime;
use App\Models\Song;
use App\Models\SongVariant;
use App\Models\Season;
use App\Models\Year;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SongVariantController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
    public function show(SongVariant $variant)
    {
        $songVariant = $variant;
        $user = Auth::check() ? Auth::User() : null;

        if (! $songVariant) {
            return redirect(route('home'))->with('warning', 'Item no exist!');
        }

        if ($songVariant->song->anime->status == 'stagged') {
            return redirect(route('home'))->with('warning', 'Paused anime!');
        }

        $comments = $songVariant->comments;
        // dd($comments[0]->user);
        $factor = 1;

        $songVariant->score = round($songVariant->averageRating * $factor, 1);

        // Is used by rating form
        $userRating = null;

        if ($user) {

            $userRating = $this->getUserRating($songVariant, $user);

            if ($userRating) {

                switch ($user->score_format) {
                    case 'POINT_100':
                        $factor = 1;
                        $userRating->formatRating = round($userRating->rating);
                        break;

                    case 'POINT_10_DECIMAL':
                        $factor = 0.1;
                        $userRating->formatRating = round($userRating->rating / 10, 1);
                        break;

                    case 'POINT_10':
                        $factor = 1 / 10;
                        $userRating->formatRating = round($userRating->rating / 10);
                        break;

                    case 'POINT_5':
                        $factor = 1 / 20;
                        // Divide the score in segments of [20, 40, 60, 80, 100]
                        $userRating->formatRating = (int) max(20, min(100, ceil($userRating->rating / 20) * 20));
                        break;

                    default:
                        $factor = 1;
                        $userRating->formatRating = round($userRating->rating);
                        break;
                }
            }
        }

        $songVariant = $this->setScoreOnlyOneVariant($songVariant, $user);

        // dd($songVariant);

        $songVariant->incrementViews();

        return view('public.variants.show', compact('songVariant', 'comments', 'userRating'));
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

    public function showVariant($animeSlug, $songSlug, $variantSlug)
    {
        $user = Auth::check() ? Auth::User() : null;
        $anime = Anime::where('slug', $animeSlug)->first();

        if (! $anime) {
            return redirect(route('/'))->with('warning', 'Anime not found!');
        }
        if (! $anime->status) {
            if ($user) {
                if (! $user->is_admin) {
                    return redirect('/')->with('danger', 'User not autorized!');
                }
            } else {
                return redirect('/')->with('danger', 'Anime status: Private');
            }
        }

        $song = Song::where('slug', $songSlug)
            ->where('anime_id', $anime->id)
            ->firstOrFail();

        $songVariant = SongVariant::where('slug', $variantSlug)
            ->where('song_id', $song->id)
            ->firstOrFail();

        // dd($songVariant);

        if (! $songVariant) {
            return redirect(route('/'))->with('warning', 'Item no exist!');
        }

        if ($songVariant->song->anime->status == 'stagged') {
            return redirect(route('/'))->with('warning', 'Paused anime!');
        }

        $comments = $songVariant->comments;
        // dd($comments[0]->user);
        $factor = 1;

        $songVariant->score = round($songVariant->averageRating * $factor, 1);

        // Is used by rating form
        $userRating = null;

        if ($user) {

            $userRating = $this->getUserRating($songVariant->id, $user->id);

            if ($userRating) {

                switch ($user->score_format) {
                    case 'POINT_100':
                        $factor = 1;
                        $userRating->formatRating = round($userRating->rating);
                        break;

                    case 'POINT_10_DECIMAL':
                        $factor = 0.1;
                        $userRating->formatRating = round($userRating->rating / 10, 1);
                        break;

                    case 'POINT_10':
                        $factor = 1 / 10;
                        $userRating->formatRating = round($userRating->rating / 10);
                        break;

                    case 'POINT_5':
                        $factor = 1 / 20;
                        // Divide the score in segments of [20, 40, 60, 80, 100]
                        $userRating->formatRating = (int) max(20, min(100, ceil($userRating->rating / 20) * 20));
                        break;

                    default:
                        $factor = 1;
                        $userRating->formatRating = round($userRating->rating);
                        break;
                }
            }
        }

        $songVariant = $this->setScoreOnlyOneVariant($songVariant, $user);

        // dd($songVariant);

        $songVariant->incrementViews();

        return view('public.variants.show', compact('songVariant', 'comments', 'userRating'));
    }

    public function rate(Request $request, SongVariant $variant)
    {
        $songVariant = $variant;
        // dd($request->all());
        if (Auth::check()) {

            $score_format = Auth::user()->score_format;

            $validator = Validator::make($request->all(), [
                'score' => 'required|numeric',
            ]);

            if ($validator->fails()) {
                $messageBag = $validator->getMessageBag();

                return redirect()
                    ->back()
                    ->with('error', $messageBag);
            } else {
                $score = $request->score;
            }

            if ($score_format === 'POINT_5') {
                // Ajustar el score según las reglas específicas para POINT_5
                $score = max(20, min(100, ceil($score / 20) * 20));
            } else {
                // Ajustar el score según las reglas comunes para POINT_100, POINT_10_DECIMAL y POINT_10
                $score = max(1, min(100, ($score_format === 'POINT_10_DECIMAL') ? round($score * 10) : round($score)));
            }

            // Validar el rango del score
            if ($score >= 1 && $score <= 100) {
                // Utilizar el score ajustado
                $songVariant->song->rate($score, Auth::User()->id);

                return redirect()->back()->with('success', 'Rated Successfully');
            } else {
                return redirect()->back()->with('warning', 'Only values between 1 and 100');
            }
        } else {
            return redirect()->route('login');
        }
    }

    public function getUserRating($variantId, $userId)
    {
        $variant = SongVariant::find($variantId);
        if (!$variant) return null;

        return DB::table('song_ratings')
            ->where('song_id', $variant->song_id)
            ->where('user_id', $userId)
            ->first(['rating']);
    }

    public function like(SongVariant $variant)
    {
        $song = $variant->song;
        $this->handleReaction($song, 1); // 1 para like
        $song->updateReactionCounters();

        return redirect()->back();
    }

    public function dislike(SongVariant $variant)
    {
        $song = $variant->song;
        $this->handleReaction($song, -1); // -1 para dislike
        $song->updateReactionCounters();

        return redirect()->back();
    }

    private function handleReaction(Song $song, int $type)
    {
        $user = Auth::user();

        // Usar la relación pivot para manejar la reacción
        $existing = $song->reactions()->where('user_id', $user->id)->first();

        if ($existing) {
            if ($existing->pivot->type === $type) {
                // Toggle off
                $song->reactions()->detach($user->id);
            } else {
                // Update type
                $song->reactions()->updateExistingPivot($user->id, ['type' => $type]);
            }
        } else {
            // New reaction
            $song->reactions()->attach($user->id, ['type' => $type]);
        }
    }

    public function seasonal(Request $request)
    {
        $currentSeason = Season::where('current', true)->first();
        $currentYear = Year::where('current', true)->first();

        return view('public.seasonal', compact('currentSeason', 'currentYear'));
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

    // New
    public function setScoreOnlyOneVariant(SongVariant $variant, User $user)
    {
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

            if ($userRating = $this->getUserRating($variant, $user)) {
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

        return $variant;
    }

    public function toggleFavorite(SongVariant $variant)
    {
        if (! Auth::check()) {
            return redirect()->back()->with('warning', 'Please login');
        }

        $user = Auth::user();
        $song = $variant->song;
        $results = $song->favorites()->toggle($user->id);
        $isFavorite = count($results['attached']) > 0;

        return redirect()->back()->with('success', $isFavorite ? 'Song added to favorites' : 'Song removed from favorites');
    }

    public function ranking()
    {
        return view('public.ranking');
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
