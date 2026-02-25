<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Song;
use App\Models\SongVariant;
use App\Services\Breadcrumb;
use Illuminate\Http\Request;

class SongVariantController extends Controller
{
    public function index(Request $request)
    {
        $query = SongVariant::query()->with('song', 'song.post', 'video');

        $currentSong = null;
        $breadcrumbItems = [
            ['name' => 'Variants', 'url' => route('admin.variants.index')],
        ];

        if ($request->filled('song_id')) {
            $query->where('song_id', $request->song_id);
            $currentSong = Song::with('post')->find($request->song_id);

            if ($currentSong) {
                $breadcrumbItems = [
                    ['name' => 'Posts', 'url' => route('admin.posts.index')],
                    ['name' => $currentSong->post->title, 'url' => route('admin.posts.show', $currentSong->post->id)],
                    ['name' => $currentSong->slug, 'url' => route('admin.songs.index', ['post_id' => $currentSong->post->id])],
                    ['name' => 'Variants', 'url' => route('admin.variants.index', ['song_id' => $currentSong->id])],
                ];
            }
        }

        $breadcrumb = Breadcrumb::generate($breadcrumbItems);
        $songVariants = $query->latest()->paginate(20);

        return view('admin.variants.index', compact('songVariants', 'currentSong', 'breadcrumb'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $song = Song::findOrFail($request->song_id);

        $latestVersion = SongVariant::where('song_id', $song->id)
            ->max('version_number');

        $newVersion = $latestVersion !== null ? $latestVersion + 1 : 1;

        $slug = 'v'.$newVersion;

        $songVariant = new SongVariant;
        $songVariant->song_id = $song->id;
        $songVariant->version_number = $newVersion;
        $songVariant->slug = $slug;
        $songVariant->season_id = $song->season_id;
        $songVariant->year_id = $song->year_id;
        $songVariant->spoiler = false;

        if ($songVariant->save()) {
            return redirect(route('admin.variants.index', ['song_id' => $song->id]))->with('success', 'Song variant added successfully');
        } else {
            return redirect(route('admin.variants.index', ['song_id' => $song->id]))->with('error', 'Error adding variant');
        }
    }

    public function show(SongVariant $songVariant)
    {
        return $songVariant;
    }

    public function edit(SongVariant $songVariant)
    {
        $song = $songVariant->song;
        $post = $song->post;

        $breadcrumb = Breadcrumb::generate([
            [
                'name' => 'Index',
                'url' => route('admin.posts.index'),
            ],
            [
                'name' => $post->title,
                'url' => route('admin.songs.index', ['post_id' => $post->id]),
            ],
            [
                'name' => $song->slug,
                'url' => route('admin.variants.index', ['song_id' => $song->id]),
            ],
            [
                'name' => $songVariant->slug,
                'url' => '',
            ],
        ]);

        return view('admin.variants.edit', compact('songVariant', 'breadcrumb'));
    }

    public function update(Request $request, SongVariant $songVariant)
    {
        $song = $songVariant->song;

        $latestVersion = SongVariant::where('song_id', $song->id)
            ->max('version_number');

        $newVersion = $latestVersion !== null ? $latestVersion + 1 : 1;

        $slug = 'v'.$newVersion;

        $songVariant->song_id = $song->id;
        $songVariant->version_number = $newVersion;
        $songVariant->slug = $slug;
        $songVariant->season_id = $song->season_id;
        $songVariant->year_id = $song->year_id;
        $songVariant->spoiler = false;

        if ($songVariant->update()) {
            return redirect(route('admin.variants.index', ['song_id' => $songVariant->song->id]))->with('success', 'Song variant updated successfully');
        } else {
            return redirect(route('admin.variants.index', ['song_id' => $songVariant->song->id]))->with('error', 'Something went wrong');
        }
    }

    public function destroy(SongVariant $songVariant)
    {
        if ($songVariant->delete()) {
            return redirect(route('admin.variants.index', ['song_id' => $songVariant->song_id]))->with('success', 'Song variant deleted successfully');
        } else {
            return redirect(route('admin.variants.index', ['song_id' => $songVariant->song_id]))->with('error', 'Error deleting variant');
        }
    }
}
