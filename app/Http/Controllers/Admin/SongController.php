<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Anime;
use App\Models\Artist;
use App\Models\Season;
use App\Models\Song;
use App\Models\Year;
use App\Services\Breadcrumb;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SongController extends Controller
{
    public function index(Request $request)
    {
        $query = Song::query()->with('anime', 'artists');

        $currentAnime = null;
        $currentArtist = null;
        $breadcrumbItems = [
            ['name' => 'Songs', 'url' => route('admin.songs.index')],
        ];

        if ($request->filled('anime_id')) {
            $query->where('anime_id', $request->anime_id);
            $currentAnime = Anime::find($request->anime_id);

            if ($currentAnime) {
                $breadcrumbItems = [
                    ['name' => 'Animes', 'url' => route('admin.animes.index')],
                    ['name' => $currentAnime->title, 'url' => route('admin.animes.show', $currentAnime->id)],
                    ['name' => 'Songs', 'url' => route('admin.songs.index', ['anime_id' => $currentAnime->id])],
                ];
            }
        }

        if ($request->filled('artist_id')) {
            $query->whereHas('artists', function ($q) use ($request) {
                $q->where('artists.id', $request->artist_id);
            });
            $currentArtist = Artist::find($request->artist_id);

            if ($currentArtist) {
                $breadcrumbItems = [
                    ['name' => 'Artists', 'url' => route('admin.artists.index')],
                    ['name' => $currentArtist->name, 'url' => route('admin.artists.index', ['q' => $currentArtist->name])], // Assuming there's no show page for artists yet, or using search
                    ['name' => 'Songs', 'url' => route('admin.songs.index', ['artist_id' => $currentArtist->id])],
                ];
            }
        }

        $breadcrumb = Breadcrumb::generate($breadcrumbItems);

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($query) use ($q) {
                $query->where('song_romaji', 'like', "%{$q}%")
                    ->orWhere('song_en', 'like', "%{$q}%")
                    ->orWhere('song_jp', 'like', "%{$q}%")
                    ->orWhere('slug', 'like', "%{$q}%")
                    ->orWhereHas('anime', function ($query) use ($q) {
                        $query->where('title', 'like', "%{$q}%");
                    });
            });
        }

        $songs = $query->latest()->paginate(20);

        return view('admin.songs.index', compact('songs', 'currentAnime', 'currentArtist', 'breadcrumb'));
    }

    public function create(Request $request)
    {
        $selectedAnimeId = $request->anime_id;
        $currentAnime = null;
        if ($selectedAnimeId) {
            $currentAnime = Anime::find($selectedAnimeId);
        }

        $breadcrumb = Breadcrumb::generate([
            [
                'name' => 'Animes',
                'url' => route('admin.animes.index'),
            ],
            [
                'name' => 'Songs',
                'url' => route('admin.songs.index'),
            ],
            [
                'name' => 'Create',
                'url' => route('admin.songs.create'),
            ],
        ]);

        $types = [
            ['name' => 'Opening', 'value' => 'OP'],
            ['name' => 'Ending', 'value' => 'ED'],
            ['name' => 'Insert', 'value' => 'INS'],
            ['name' => 'Other', 'value' => 'OTH'],
        ];

        $seasons = Season::all();
        $years = Year::all();

        return view('admin.songs.create', compact('breadcrumb', 'types', 'seasons', 'years', 'selectedAnimeId', 'currentAnime'));
    }

    public function store(Request $request)
    {
        $song = new Song;
        if ($request->filled('song_romaji')) {
            [$name_romaji, $name_jp_parsed] = $this->parseName($request->song_romaji);

            $song->song_romaji = $name_romaji;
            if ($name_jp_parsed) {
                $song->song_jp = $name_jp_parsed;
            }
        }

        if ($request->filled('song_en')) {
            [$name_en, $name_jp_parsed] = $this->parseName($request->song_en);

            $song->song_en = $name_en;
            if ($name_jp_parsed) {
                $song->song_jp = $name_jp_parsed;
            }
        }

        if ($request->filled('song_jp')) {
            $song->song_jp = trim($request->song_jp) ?: null;
        }

        $song->song_romaji = $song->song_romaji ?: null;
        $song->song_en = $song->song_en ?: null;
        $song->song_jp = $song->song_jp ?: null;

        $song->anime_id = $request->anime_id;
        $song->season_id = $request->season_id;
        $song->year_id = $request->year_id;
        $song->type = $request->type;

        $rawNamesList = (explode(',', $request->artists));

        $artistsIds = [];
        // dd($song);

        // artists save section
        foreach ($rawNamesList as $rawName) {
            // Separate the name and name_jp using the parseName method, remove extra spaces
            [$name, $name_jp] = $this->parseName($rawName);

            // dd($name, $name_jp);

            if ($name != '' && $name != null) {
                $artist = Artist::firstOrCreate(
                    [
                        'slug' => Str::slug($name),
                    ],
                    [
                        'name' => $name,
                        'name_jp' => $name_jp ?: null,
                    ]
                );
                $artistsIds[] = $artist->id;
            }
        }

        $latestVersion = Song::where('anime_id', $request->anime_id)
            ->where('type', $request->type)
            ->max('theme_num');

        if (($request->theme_num != null) && ($request->theme_num > $latestVersion)) {
            $song->theme_num = $request->theme_num;
        } else {
            $newVersion = $latestVersion !== null ? $latestVersion + 1 : 1;

            $song->theme_num = $newVersion;
        }

        $song->slug = $song->type.$song->theme_num;
        $song->status = $this->resolveStatus($request);

        if ($song->save()) {
            $song->artists()->sync($artistsIds);

            return redirect(route('admin.songs.index', ['anime_id' => $request->anime_id]))->with('success', 'Song added successfully');
        } else {
            return redirect(route('admin.songs.index'))->with('error', 'error');
        }
    }

    public function parseName($rawName)
    {
        // Expresión regular para capturar el texto antes y dentro de los paréntesis
        if (preg_match('/^(.*?)\s*\((.*?)\)$/u', trim($rawName), $matches)) {
            $name = trim(preg_replace('/\s+/', ' ', $matches[1]));
            $name_jp = trim(preg_replace('/\s+/', ' ', $matches[2]));
        } else {
            // Si no hay paréntesis, asumimos que solo hay nombre
            $name = trim($rawName);
            $name_jp = null;
        }

        return [$name ?: null, $name_jp ?: null];
    }

    public function show(Song $song)
    {
        $breadcrumb = Breadcrumb::generate([
            [
                'name' => 'Animes',
                'url' => route('admin.animes.index'),
            ],
            [
                'name' => $song->anime->title,
                'url' => route('admin.animes.show', $song->anime_id),
            ],
            [
                'name' => 'Songs',
                'url' => route('admin.songs.index', ['anime_id' => $song->anime_id]),
            ],
            [
                'name' => 'Show',
                'url' => route('admin.songs.show', $song->id),
            ],
        ]);

        return view('admin.songs.show', compact('song', 'breadcrumb'));
    }

    public function edit(Song $song)
    {
        $breadcrumb = Breadcrumb::generate([
            [
                'name' => 'Animes',
                'url' => route('admin.animes.index'),
            ],
            [
                'name' => $song->anime->title,
                'url' => route('admin.animes.show', $song->anime_id),
            ],
            [
                'name' => 'Songs',
                'url' => route('admin.songs.index', ['anime_id' => $song->anime_id]),
            ],
            [
                'name' => 'Edit',
                'url' => route('admin.songs.edit', $song->id),
            ],
        ]);
        /* $artists = Artist::all(); */
        $seasons = Season::all();
        $years = Year::all();
        $types = [
            ['name' => 'Opening', 'value' => 'OP'],
            ['name' => 'Ending', 'value' => 'ED'],
            ['name' => 'Insert', 'value' => 'INS'],
            ['name' => 'Other', 'value' => 'OTH'],
        ];

        return view('admin.songs.edit', compact('song', /* 'artists', */ 'types', 'seasons', 'years', 'breadcrumb'));
    }

    public function update(Request $request, $songId)
    {
        $song = Song::with('anime')->findOrFail($songId);

        $song->song_romaji = trim($request->song_romaji) ?: null;
        $song->song_jp = trim($request->song_jp) ?: null;
        $song->song_en = trim($request->song_en) ?: null;
        $song->anime_id = $song->anime->id;
        $song->season_id = $request->season_id;
        $song->year_id = $request->year_id;
        $song->type = $request->type;

        $artistsNames = (explode(',', $request->artists));

        $artistsIds = [];

        foreach ($artistsNames as $rawName) {
            [$name, $name_jp] = $this->parseName($rawName);

            if ($name) {
                $artist = Artist::firstOrCreate(
                    [
                        'slug' => Str::slug($name),
                    ],
                    [
                        'name' => $name,
                        'name_jp' => $name_jp ?: null,
                    ]
                );
                $artistsIds[] = $artist->id;
            }
        }

        $latestVersion = Song::where('anime_id', $song->anime_id)
            ->where('type', $request->type)
            ->where('id', '!=', $song->id)
            ->max('theme_num');

        if (($request->theme_num != null) && ($request->theme_num > $latestVersion)) {
            $song->theme_num = $request->theme_num;
        } else {
            $newVersion = $latestVersion !== null ? $latestVersion + 1 : 1;
            $song->theme_num = $newVersion;
        }

        $song->slug = $song->type.$song->theme_num;
        $song->status = $this->resolveStatus($request);

        // dd($song);
        if ($song->update()) {
            $song->artists()->sync($artistsIds);

            return redirect(route('admin.songs.index', ['anime_id' => $song->anime_id]))->with('success', 'Song updated successfully');
        } else {
            return redirect(route('admin.songs.index'))->with('error', 'error, something went wrong');
        }
    }

    /**
     * Get the latest theme number for a specific anime and type.
     */
    public function getLatestNumber(Request $request)
    {
        $request->validate([
            'anime_id' => 'required|exists:animes,id',
            'type' => 'required|string|in:OP,ED,INS,OTH',
        ]);

        $latestVersion = Song::where('anime_id', $request->anime_id)
            ->where('type', $request->type)
            ->max('theme_num');

        $nextNumber = $latestVersion !== null ? $latestVersion + 1 : 1;

        return response()->json(['next_number' => $nextNumber]);
    }

    public function destroy(Song $song)
    {
        $song->artists()->detach();
        if ($song->delete()) {

            return redirect()->back()->with('success', 'Song '.$song->id.' has been deleted');
        } else {
            return redirect()->back()->with('error', 'A error has been ocurred');
        }
    }

    public function decodeUnicodeIfNeeded($string)
    {
        // Validar si la cadena contiene secuencias Unicode (\uXXXX)
        if (preg_match('/\\\u[0-9a-fA-F]{4}/', $string)) {
            // Decodificar secuencias Unicode.
            return json_decode('"'.$string.'"');
        }

        return $string;
    }

    /**
     * Determine the status based on the user's role.
     */
    private function resolveStatus(Request $request): bool
    {
        $user = \Illuminate\Support\Facades\Auth::user();

        if ($user->hasRole('admin') || $user->hasRole('editor')) {
            return (bool) $request->status;
        }

        return false;
    }
}
