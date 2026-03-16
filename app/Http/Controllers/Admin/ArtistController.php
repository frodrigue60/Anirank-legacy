<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Artist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ArtistController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $breadcrumb = [
            [
                'name' => 'Artists',
                'url' => route('admin.artists.index'),
            ],
        ];

        $query = Artist::query();
        if ($request->filled('q')) {
            $query->where('name', 'like', "%{$request->q}%")
                ->orWhere('name_jp', 'like', "%{$request->q}%");
        }

        $artists = $query->paginate(15);

        return view('admin.artists.index', compact('artists', 'breadcrumb'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $breadcrumb = [
            [
                'name' => 'Artists',
                'url' => route('admin.artists.index'),
            ],
            [
                'name' => 'Create',
                'url' => '',
            ],
        ];

        return view('admin.artists.create', compact('breadcrumb'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100', // Increased length just in case
            'name_jp' => 'nullable|string|max:100',
            'avatar' => 'nullable|image|max:2048',
            'avatar_src' => 'nullable|url|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors($validator);
        }

        $name = trim(preg_replace('/\s+/', ' ', $request->name));

        if ($this->artistExists($name)) {
            return redirect(route('admin.artists.index'))->with('warning', 'Artist '.$name.' already exists!');
        }

        $artist = new Artist;
        $artist->name = $name;
        // dd($request->all());
        $artist->name_jp = $request->filled('name_jp') ? trim(preg_replace('/\s+/', ' ', $request->name_jp)) : null;

        $artist->slug = $this->generateUniqueSlug($request->name);

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('artists', config('filesystems.default'));
            $artist->avatar = $path;
        } elseif ($request->filled('avatar_src')) {
            $artist->avatar = $request->avatar_src;
        }

        $artist->status = $this->resolveStatus($request);

        if ($artist->save()) {

            // Automate avatar generation for new artists ONLY if none was provided
            if (!$artist->avatar) {
                $this->generateThumbnail($artist);
            }

            return redirect(route('admin.artists.index'))->with('success', 'Data has been inserted successfully');
        }

        return redirect(route('admin.artists.index'))->with('error', 'Something went wrong');
    }

    private function artistExists($name)
    {
        return Artist::where('name', $name)
            ->where('slug', Str::slug($name))
            ->exists();
    }

    public function show(Artist $artist)
    {
        //
    }

    public function edit(Artist $artist)
    {
        $breadcrumb = [
            [
                'name' => 'Artists',
                'url' => route('admin.artists.index'),
            ],
            [
                'name' => 'Edit',
                'url' => '',
            ],
        ];

        return view('admin.artists.edit', compact('artist', 'breadcrumb'));
    }

    public function update(Request $request, Artist $artist)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'name_jp' => 'nullable|string|max:100',
            'avatar' => 'nullable|image|max:2048',
            'avatar_src' => 'nullable|url|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors($validator);
        }

        $name = trim(preg_replace('/\s+/', ' ', $request->name));
        $artist->name = $name;

        $artist->slug = $this->generateUniqueSlug($request->name);

        $artist->name_jp = $request->filled('name_jp') ? trim(preg_replace('/\s+/', ' ', $request->name_jp)) : null;

        // Handle Avatar Update
        if ($request->hasFile('avatar')) {
            // Delete old file if it exists and is not a URL
            if ($artist->avatar && !filter_var($artist->avatar, FILTER_VALIDATE_URL)) {
                Storage::disk(config('filesystems.default'))->delete($artist->avatar);
            }
            $path = $request->file('avatar')->store('artists', config('filesystems.default'));
            $artist->avatar = $path;
        } elseif ($request->filled('avatar_src')) {
            // Delete old file if it's not a URL
            if ($artist->avatar && !filter_var($artist->avatar, FILTER_VALIDATE_URL)) {
                Storage::disk(config('filesystems.default'))->delete($artist->avatar);
            }
            $artist->avatar = $request->avatar_src;
        }

        $artist->status = $this->resolveStatus($request);

        if ($artist->save()) {
            return redirect(route('admin.artists.index'))->with('success', 'Data has been updated successfully');
        }

        return redirect(route('admin.artists.index'))->with('error', 'Something went wrong');
    }

    public function destroy(Artist $artist)
    {
        $artist->delete();

        return redirect(route('admin.artists.index'))->with('success', 'Data deleted');
    }

    /**
     * Generate a thumbnail for a single artist.
     */
    public function generateThumbnail(Artist $artist)
    {
        try {
            $name = urlencode($artist->name);
            // Generate a random vibrant background color
            $background = str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
            $url = "https://ui-avatars.com/api/?name={$name}&color=fff&background={$background}&size=512";

            $response = Http::timeout(5)->get($url);

            if ($response->successful()) {
                $file_name = $artist->slug.'-avatar-'.time().'.png';
                $path = 'artists/'.$file_name;

                Storage::disk(config('filesystems.default'))->put($path, $response->body());
                
                if ($artist->avatar && Storage::disk(config('filesystems.default'))->exists($artist->avatar)) {
                    Storage::disk(config('filesystems.default'))->delete($artist->avatar);
                }
                $artist->avatar = $path;
                $artist->save();

                return true;
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning("Could not fetch avatar for artist {$artist->id}: ".$e->getMessage());
        }

        return false;
    }

    /**
     * Generate thumbnails for all artists that don't have one.
     */
    public function generateAllThumbnails()
    {
        // Get artists that don't have a thumbnail image
        $artists = Artist::whereNull('avatar')->get();

        $count = 0;
        foreach ($artists as $artist) {
            if ($this->generateThumbnail($artist)) {
                $count++;
            }
        }

        return redirect(route('admin.artists.index'))->with('success', "Generated {$count} thumbnails successfully.");
    }

    private function generateUniqueSlug($name)
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $count = 1;

        while (Artist::where('slug', $slug)->exists()) {
            $slug = $baseSlug.'-'.$count;
            $count++;
        }

        return $slug;
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
