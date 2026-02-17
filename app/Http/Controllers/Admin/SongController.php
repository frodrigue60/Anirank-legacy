<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Artist;
use App\Models\Season;
use App\Models\Song;
use Illuminate\Http\Request;
use App\Models\SongVariant;
use App\Models\Year;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use App\Models\Post;

use App\Services\Breadcrumb;

class SongController extends Controller
{
    public function index(Request $request)
    {
        $query = Song::query()->with('post', 'artists');

        $currentPost = null;
        $currentArtist = null;
        $breadcrumbItems = [
            ['name' => 'Songs', 'url' => route('admin.songs.index')]
        ];

        if ($request->filled('post_id')) {
            $query->where('post_id', $request->post_id);
            $currentPost = Post::find($request->post_id);

            if ($currentPost) {
                $breadcrumbItems = [
                    ['name' => 'Posts', 'url' => route('admin.posts.index')],
                    ['name' => $currentPost->title, 'url' => route('admin.posts.show', $currentPost->id)],
                    ['name' => 'Songs', 'url' => route('admin.songs.index', ['post_id' => $currentPost->id])]
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
                    ['name' => 'Songs', 'url' => route('admin.songs.index', ['artist_id' => $currentArtist->id])]
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
                    ->orWhereHas('post', function ($query) use ($q) {
                        $query->where('title', 'like', "%{$q}%");
                    });
            });
        }

        $songs = $query->latest()->paginate(20);
        return view('admin.songs.index', compact('songs', 'currentPost', 'currentArtist', 'breadcrumb'));
    }

    public function create(Request $request)
    {
        $selectedPostId = $request->post_id;
        $currentPost = null;
        if ($selectedPostId) {
            $currentPost = Post::find($selectedPostId);
        }

        $breadcrumb = Breadcrumb::generate([
            [
                'name' => 'Posts',
                'url' => route('admin.posts.index')
            ],
            [
                'name' => 'Songs',
                'url' => route('admin.songs.index')
            ],
            [
                'name' => 'Create',
                'url' => route('admin.songs.create')
            ]
        ]);

        $types = [
            ['name' => 'Opening', 'value' => 'OP'],
            ['name' => 'Ending', 'value' => 'ED'],
            ['name' => 'Insert', 'value' => 'INS'],
            ['name' => 'Other', 'value' => 'OTH']
        ];

        $seasons = Season::all();
        $years = Year::all();

        $posts = Post::orderBy('title')->get(['id', 'title']);

        return view('admin.songs.create', compact('breadcrumb', 'posts', 'types', 'seasons', 'years', 'selectedPostId', 'currentPost'));
    }

    public function store(Request $request)
    {
        $name_romaji = null;
        $name_jp = null;
        $song = new Song();

        if ($request->song_romaji != null && $request->song_romaji != '') {
            list($name_romaji, $name_jp) = $this->parseName($request->song_romaji);

            $song->song_romaji = $name_romaji;
            $song->song_jp = $name_jp;
        }

        if ($request->song_en != null && $request->song_en != '') {
            list($name_en, $name_jp) = $this->parseName($request->song_en);

            $song->song_en = $name_en;
            $song->song_jp = $name_jp;
        }
        //$song->song_romaji = Str::of($request->song_romaji)->trim();
        //$song->song_jp = Str::of($request->song_jp)->trim();
        //$song->song_en = Str::of($request->song_en)->trim();

        $song->post_id =  $request->post_id;
        $song->season_id = $request->season_id;
        $song->year_id = $request->year_id;
        $song->type = $request->type;

        $rawNamesList = (explode(',', $request->artists));

        $artistsIds = [];
        //dd($song);

        //artists save section
        foreach ($rawNamesList as $rawName) {
            //Separate the name and name_jp using the parseName method, remove extra spaces
            list($name, $name_jp) = $this->parseName($rawName);

            //dd($name, $name_jp);

            if ($name != '' && $name != null) {
                $artist = Artist::firstOrCreate(
                    [
                        'name' =>  $name,
                        'slug' => Str::slug($name),
                    ],
                    [
                        'name' =>  $name,
                        'name_jp' => $name_jp ? $name_jp : null
                    ]
                );
                $artistsIds[] = $artist->id;
            }
        }

        $latestVersion = Song::where('post_id', $request->post_id)
            ->where('type', $request->type)
            ->max('theme_num');

        if (($request->theme_num != null) && ($request->theme_num > $latestVersion)) {
            $song->theme_num = $request->theme_num;
        } else {
            $newVersion = $latestVersion !== null ? $latestVersion + 1 : 1;

            $song->theme_num = $newVersion;
        }

        $song->slug = $song->type . $song->theme_num;
        //dd($song);
        if ($song->save()) {
            $song->artists()->sync($artistsIds);
            return redirect(route('admin.songs.index', ['post_id' => $request->post_id]))->with('success', 'Song added successfully');
        } else {
            return redirect(route('admin.songs.index'))->with('error', 'error');
        }
    }


    function parseName($rawName)
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

        return [$name, $name_jp];
    }



    public function show($id)
    {
        //
    }

    public function edit(Song $song)
    {
        $breadcrumb = Breadcrumb::generate([
            [
                'name' => 'Posts',
                'url' => route('admin.posts.index')
            ],
            [
                'name' => $song->post->title,
                'url' => route('admin.posts.show', $song->post_id)
            ],
            [
                'name' => 'Songs',
                'url' => route('admin.songs.index', ['post_id' => $song->post_id])
            ],
            [
                'name' => 'Edit',
                'url' => route('admin.songs.edit', $song->id)
            ]
        ]);
        /* $artists = Artist::all(); */
        $posts = Post::all('id', 'title');
        $seasons = Season::all();
        $years = Year::all();
        $types = [
            ['name' => 'Opening', 'value' => 'OP'],
            ['name' => 'Ending', 'value' => 'ED'],
            ['name' => 'Insert', 'value' => 'INS'],
            ['name' => 'Other', 'value' => 'OTH']
        ];

        return view('admin.songs.edit', compact('song', /* 'artists', */ 'types', 'seasons', 'years', 'breadcrumb', 'posts'));
    }

    public function update(Request $request, $songId)
    {
        //dd($request->all());
        $song = Song::with('post')->findOrFail($songId);

        $song->song_romaji = Str::of($request->song_romaji)->trim();
        $song->song_jp = Str::of($request->song_jp)->trim();
        $song->song_en = Str::of($request->song_en)->trim();
        $song->post_id = $song->post->id;
        $song->season_id = $request->season_id;
        $song->year_id = $request->year_id;
        $song->type = $request->type;

        $artistsNames = (explode(',', $request->artists));

        $artistsIds = [];

        foreach ($artistsNames as $name) {
            $name = preg_replace('/\s+/', ' ', $name);
            $artist = Artist::firstOrCreate(
                [
                    'slug' => Str::slug($name),
                ],
                [
                    'name' =>  $name,
                ]
            );
            $artistsIds[] = $artist->id;
        }

        $latestVersion = Song::where('post_id', $song->post_id)
            ->where('type', $request->type)
            ->where('id', '!=', $song->id)
            ->max('theme_num');

        if (($request->theme_num != null) && ($request->theme_num > $latestVersion)) {
            $song->theme_num = $request->theme_num;
        } else {
            $newVersion = $latestVersion !== null ? $latestVersion + 1 : 1;
            $song->theme_num = $newVersion;
        }

        $song->slug = $song->type . $song->theme_num;
        //dd($song);
        if ($song->update()) {
            $song->artists()->sync($artistsIds);
            return redirect(route('admin.songs.index', ['post_id' => $song->post_id]))->with('success', 'Song updated successfully');
        } else {
            return redirect(route('admin.songs.index'))->with('error', 'error, something went wrong');
        }
    }

    public function destroy($id)
    {
        $song = Song::find($id);
        $song->artists()->detach();
        if ($song->delete()) {

            return redirect()->back()->with('success', 'Song ' . $song->id . ' has been deleted');
        } else {
            return redirect()->back()->with('error', 'A error has been ocurred');
        }
    }

    public function decodeUnicodeIfNeeded($string)
    {
        // Validar si la cadena contiene secuencias Unicode (\uXXXX)
        if (preg_match('/\\\u[0-9a-fA-F]{4}/', $string)) {
            // Decodificar secuencias Unicode.
            return json_decode('"' . $string . '"');
        }
        return $string;
    }
}
