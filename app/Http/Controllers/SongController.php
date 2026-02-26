<?php

namespace App\Http\Controllers;

use App\Models\Song;
use App\Models\Anime;
use App\Models\Year;
use App\Models\Season;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SongController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $years = Year::select('id', 'name')->orderBy('name', 'desc')->get();
        $seasons = Season::select('id', 'name')->get();
        $types = $this->filterTypesSortChar()['types'];
        $sortMethods = $this->filterTypesSortChar()['sortMethods'];

        return view('public.songs.index', compact('seasons', 'years', 'sortMethods', 'types'));
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
     * @param  \Illuminate\Http\Request  $request
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
    public function show(Song $song)
    {
        $song->load(['songVariants.video', 'anime']);
        $anime = $song->anime;
        $user = Auth::user();

        if (!$anime->status) {
            if ($user && $user->isAdmin()) {
                // Admin can view private animes
            } else {
                return redirect('/')->with('danger', $user ? 'User not autorized!' : 'Anime status: Private');
            }
        }

        $song->incrementViews();

        return view('public.songs.show', compact('song', 'anime'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Song $song)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
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

    public function seasonal()
    {
        $currentSeason = Season::where('current', true)->first();
        $currentYear = Year::where('current', true)->first();

        //dd($currentSeason, $currentYear);

        return view('public.seasonal', compact('currentSeason', 'currentYear'/* , 'openings', 'endings' */));
    }

    public function ranking()
    {
        return view('public.ranking');
    }

    public function showAnimeSong(Anime $anime, Song $song)
    {
        $user = Auth::user();

        if (!$anime->status) {
            if ($user && $user->isAdmin()) {
                // Admin can view private animes
            } else {
                return redirect('/')->with('danger', $user ? 'User not autorized!' : 'Anime status: Private');
            }
        }

        $song->load(['songVariants.video']);

        $song->incrementViews();

        return view('public.songs.show', compact('song', 'anime'));
    }

    public function filterTypesSortChar()
    {
        $filters = [
            ['name' => 'All', 'value' => 'all'],
            ['name' => 'Only Rated', 'value' => 'rated']
        ];

        $types = [
            ['name' => 'Opening', 'value' => 'OP'],
            ['name' => 'Ending', 'value' => 'ED'],
            ['name' => 'Insert', 'value' => 'INS'],
            ['name' => 'Other', 'value' => 'OTH'],
        ];

        $sortMethods = [
            ['name' => 'Recent', 'value' => 'recent'],
            ['name' => 'Title', 'value' => 'title'],
            ['name' => 'Score', 'value' => 'averageRating'],
            ['name' => 'Views', 'value' => 'view_count'],
            ['name' => 'Popular', 'value' => 'likeCount']
        ];

        $characters = range('A', 'Z');

        $data = [
            'filters' => $filters,
            'types' => $types,
            'sortMethods' => $sortMethods,
            'characters' => $characters
        ];
        return $data;
    }
}
