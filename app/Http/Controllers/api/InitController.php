<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Format;
use App\Models\Genre;
use App\Models\Season;
use App\Models\Year;
use Illuminate\Http\Request;

class InitController extends Controller
{
    /**
     * Devuelve los datos maestros necesarios para cargar la SPA.
     */
    public function index()
    {
        return response()->json([
            'current_year' => Year::where('current', true)->first(),
            'current_season' => Season::where('current', true)->first(),
            'years' => Year::orderBy('name', 'desc')->get(),
            'seasons' => Season::all(),
            'formats' => Format::all(),
            'genres' => Genre::orderBy('name', 'asc')->get(),
        ]);
    }
}
