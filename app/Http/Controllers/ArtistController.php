<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use App\Models\Season;
use App\Models\Year;

class ArtistController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('public.artists.index');
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Artist $artist)
    {
        $years = Year::all()->sortByDesc('name');
        $seasons = Season::all()->sortByDesc('name');

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
            ['name' => 'Popular', 'value' => 'likeCount'],
        ];

        $artist->load('songs');

        return view('public.artists.show', compact('artist', 'seasons', 'years', 'sortMethods', 'types'));
    }
}
