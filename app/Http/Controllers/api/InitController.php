<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Format;
use App\Models\Genre;
use App\Models\Season;
use App\Models\Year;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class InitController extends Controller
{
    /**
     * Devuelve los datos maestros necesarios para cargar la SPA.
     */
    public function index()
    {
        $data = Cache::remember('api_init_data', 86400, function () {
            return [
                'current_year' => Year::where('current', true)->first(),
                'current_season' => Season::where('current', true)->first(),
                'years' => Year::orderBy('name', 'desc')->get(),
                'seasons' => Season::all(),
                'formats' => Format::all(),
                'genres' => Genre::orderBy('name', 'asc')->get(),
            ];
        });

        return response()->json($data);
    }
}
