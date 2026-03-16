<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SongVariant;
use App\Models\Video;
use App\Services\Breadcrumb;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class VideoController extends Controller
{
    public function index(Request $request)
    {
        $songVariant = SongVariant::with('video')->findOrFail($request->variant_id);
        $video = $songVariant->video;

        $breadcrumb = Breadcrumb::generate([
            [
                'name' => 'Animes',
                'url' => route('admin.animes.index'),
            ],
            [
                'name' => $songVariant->song->anime->title,
                'url' => route('admin.songs.index', ['anime_id' => $songVariant->song->anime->id]),
            ],
            [
                'name' => $songVariant->song->slug,
                'url' => route('admin.variants.index', ['song_id' => $songVariant->song->id]),
            ],
            [
                'name' => $songVariant->slug.' - '.'video',
                'url' => '',
            ],
        ]);

        return view('admin.videos.index', compact('songVariant', 'breadcrumb', 'video'));
    }

    public function create(Request $request)
    {
        $variantId = $request->query('variant_id') ?? $request->query('variant');

        if (! $variantId) {
            return redirect(route('admin.animes.index'))->with('error', 'Variant ID is required to add a video.');
        }

        $songVariant = SongVariant::with('song', 'song.anime')->findOrFail($variantId);
        $song = $songVariant->song;
        $anime = $song->anime;

        $breadcrumb = Breadcrumb::generate([
            [
                'name' => 'Index',
                'url' => route('admin.animes.index'),
            ],
            [
                'name' => $anime->title,
                'url' => route('admin.songs.index', ['anime_id' => $anime->id]),
            ],
            [
                'name' => $song->slug,
                'url' => route('admin.variants.index', ['song_id' => $song->id]),
            ],
            [
                'name' => $songVariant->slug.' - '.'video',
                'url' => '',
            ],
        ]);

        return view('admin.videos.create', compact('song', 'songVariant', 'breadcrumb'));
    }

    public function store(Request $request)
    {
        $songVariant = SongVariant::with('song.anime')->findOrFail($request->song_variant_id);
        $song = $songVariant->song;
        $anime = $song->anime;

        try {
            $video = new Video;
            $video->song_variant_id = $songVariant->id;

            if ($request->hasFile('video')) {
                $validator = Validator::make($request->all(), [
                    'video' => 'mimes:webm,mp4',
                ]);

                if ($validator->fails()) {
                    $request->flash();

                    return Redirect::back()->with('error', $validator->getMessageBag());
                }

                $yearFolder = Str::slug($song->year?->name ?? 'unknown');
                $seasonFolder = Str::slug($song->season?->name ?? 'unknown');
                $path = "videos/{$yearFolder}/{$seasonFolder}/";

                $mimeType = $request->video->getMimeType();
                $extension = $this->getExtensionFromMimeType($mimeType);

                $file_name = ($anime->slug ?? 'untitled').'-'.($song->slug ?? 'song').'-'.($songVariant->slug ?? 'default').'.'.$extension;

                // Store file FIRST — only save DB record if storage succeeds
                $request->video->storeAs($path, $file_name);

                $video->video_src = $path.$file_name;
            } else {
                $validator = Validator::make($request->all(), [
                    'embed' => 'required',
                ]);

                if ($validator->fails()) {
                    $request->flash();

                    return Redirect::back()->with('error', $validator->getMessageBag());
                }

                $video->embed_code = $request->embed;
            }

            $video->status = $this->resolveStatus($request);
            $video->save();

            return redirect(route('admin.variants.index', ['song_id' => $song->id]))->with('success', 'Video saved successfully');
        } catch (\Throwable $e) {
            return redirect(route('admin.variants.index', ['song_id' => $song->id]))->with('error', $e->getMessage());
        }
    }

    public function show(Video $video)
    {
        try {
            dd($video);
        } catch (\Throwable $e) {
            dd($e);
        }
    }

    public function edit(Video $video)
    {
        try {
            $anime = $video->songVariant->song->anime;
            $song = $video->songVariant->song;

            $breadcrumb = Breadcrumb::generate([
                [
                    'name' => 'Animes',
                    'url' => route('admin.animes.index'),
                ],
                [
                    'name' => $anime->title,
                    'url' => route('admin.songs.index', ['anime_id' => $anime->id]),
                ],
                [
                    'name' => $song->slug,
                    'url' => route('admin.variants.index', ['song_id' => $song->id]),
                ],
                [
                    'name' => $video->id,
                    'url' => route('admin.videos.edit', $video->id),
                ],
            ]);

            return view('admin.videos.edit', compact('video', 'breadcrumb'));
        } catch (\Throwable $e) {
            dd($e);
        }
    }

    public function update(Request $request, Video $video)
    {
        try {
            $songVariant = $video->songVariant;
            $song = $songVariant->song;
            $anime = $song->anime;

            if ($request->hasFile('video')) {
                $validator = Validator::make($request->all(), [
                    'video' => 'mimes:webm,mp4',
                ]);

                if ($validator->fails()) {
                    $request->flash();

                    return Redirect::back()->with('error', $validator->getMessageBag());
                }

                $yearFolder = Str::slug($song->year?->name ?? 'unknown');
                $seasonFolder = Str::slug($song->season?->name ?? 'unknown');
                $path = "videos/{$yearFolder}/{$seasonFolder}/";

                $mimeType = $request->video->getMimeType();
                $extension = $this->getExtensionFromMimeType($mimeType);

                $file_name = ($anime->slug ?? 'untitled').'-'.($song->slug ?? 'song').'-'.($songVariant->slug ?? 'default').'.'.$extension;

                // Store new file FIRST
                $request->video->storeAs($path, $file_name);

                // Delete old file after new one is safely stored
                $oldFile = $video->video_src;
                if ($oldFile && Storage::disk()->exists($oldFile)) {
                    Storage::disk()->delete($oldFile);
                }

                $video->video_src = $path.$file_name;
            } else {
                $validator = Validator::make($request->all(), [
                    'embed' => 'required',
                ]);

                if ($validator->fails()) {
                    $request->flash();

                    return Redirect::back()->with('error', $validator->getMessageBag());
                }

                $video->embed_code = $request->embed;
                $video->video_src = null;
            }

            $video->status = $this->resolveStatus($request);
            $video->update();

            return redirect(route('admin.variants.index', ['song_id' => $song->id]))->with('success', 'Video updated successfully');
        } catch (\Throwable $e) {
            return redirect(route('admin.variants.index', ['song_id' => $song->id]))->with('error', $e->getMessage());
        }
    }

    public function destroy(Video $video)
    {
        $song_variant = $video->songVariant;
        $song = $song_variant->song;
        try {
            if ($video->delete()) {
                return redirect(route('admin.variants.index', ['song_id' => $song->id]))->with('success', 'Video deleted successfully');
            }
        } catch (\Throwable $e) {
            return redirect(route('admin.variants.index', ['song_id' => $song->id]))
                ->with('error', $e->getMessage());
        }
    }

    protected function getExtensionFromMimeType($mimeType)
    {
        $mimeMap = [
            'image/jpeg' => 'jpg',
            'image/webp' => 'webp',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'application/pdf' => 'pdf',
            'video/mp4' => 'mp4',
            'video/quicktime' => 'mov',
            'video/webm' => 'webm',
            'audio/mpeg' => 'mp3',
            'audio/wav' => 'wav',
        ];

        return $mimeMap[$mimeType] ?? 'bin';
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
