<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Anime;
use App\Models\DailyMetric;
use App\Models\ExternalLink;
use App\Models\Format;
use App\Models\Genre;
use App\Models\Producer;
use App\Models\Season;
use App\Models\Song;
use App\Models\Studio;
use App\Models\User;
use App\Models\Year;
use App\Services\Breadcrumb;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Intervention\Image\ImageManagerStatic as Image;

class AnimeController extends Controller
{
    public function autocomplete(Request $request)
    {
        $q = $request->q;
        $animes = Anime::where('title', 'like', "%{$q}%")
            ->latest()
            ->limit(10)
            ->get(['id', 'title']);

        return response()->json($animes);
    }

    /**
     * Reusable Guzzle client for AniList API calls.
     */
    private ?Client $httpClient = null;

    private function httpClient(): Client
    {
        return $this->httpClient ??= new Client;
    }

    // ──────────────────────────────────────────────
    //  CRUD
    // ──────────────────────────────────────────────

    public function index(Request $request)
    {
        $breadcrumb = Breadcrumb::generate([
            ['name' => 'Animes', 'url' => route('admin.animes.index')],
        ]);

        $query = Anime::query();

        if ($request->filled('q')) {
            $query->where('title', 'like', "%{$request->q}%")
                ->orWhere('description', 'like', "%{$request->q}%");
        }

        $animes = $query->latest()->paginate(20);

        return view('admin.animes.index', compact('animes', 'breadcrumb'));
    }

    public function create()
    {
        $seasons = Season::all();
        $years = Year::all();
        $types = self::songTypes();
        $animeStatus = self::animeStatuses();

        $breadcrumb = Breadcrumb::generate([
            ['name' => 'Animes', 'url' => route('admin.animes.index')],
            ['name' => 'Create anime', 'url' => ''],
        ]);

        return view('admin.animes.create', compact('years', 'seasons', 'types', 'animeStatus', 'breadcrumb'));
    }

    public function store(Request $request)
    {
        $anime = new Anime;
        $anime->title = $request->title;
        $anime->slug = Str::slug($request->title);
        $anime->description = $request->description;
        $anime->year_id = $request->year;
        $anime->season_id = $request->season;
        $anime->status = $this->resolveAnimeStatus($request);

        $this->storeAnimeImages($anime, $request);

        if ($anime->save()) {
            return redirect(route('admin.songs.index', ['anime_id' => $anime->id]))
                ->with('success', 'Anime created successfully');
        }

        return redirect(route('admin.animes.index'))->with('error', 'Something went wrong!');
    }

    public function show(Anime $anime)
    {
        $score_format = Auth::user()->score_format;

        $breadcrumb = Breadcrumb::generate([
            ['name' => 'Animes', 'url' => route('admin.animes.index')],
            ['name' => $anime->title, 'url' => route('admin.animes.show', $anime->id)],
        ]);

        $ops = $anime->songs->where('type', 'OP');
        $eds = $anime->songs->where('type', 'ED');

        return view('admin.animes.show', compact('anime', 'score_format', 'ops', 'eds', 'breadcrumb'));
    }

    public function edit(Anime $anime)
    {
        $seasons = Season::all();
        $years = Year::all();
        $types = self::songTypes();
        $animeStatus = self::animeStatuses();

        $breadcrumb = Breadcrumb::generate([
            ['name' => 'Animes', 'url' => route('admin.animes.index')],
            ['name' => $anime->title, 'url' => ''],
        ]);

        return view('admin.animes.edit', compact('anime', 'types', 'animeStatus', 'breadcrumb', 'years', 'seasons'));
    }

    public function update(Request $request, Anime $anime)
    {
        $old_thumbnail = $anime->thumbnail;
        $old_banner = $anime->banner;

        $anime->title = $request->title;
        $anime->slug = Str::slug($request->title);
        $anime->description = $request->description;
        $anime->status = $this->resolveAnimeStatus($request);

        $this->storeAnimeImages($anime, $request);

        if ($anime->update()) {
            return redirect(route('admin.animes.index'))->with('success', 'Anime Updated Successfully');
        }

        return redirect(route('admin.animes.index'))->with('error', 'Something went wrong');
    }

    public function destroy(Anime $anime)
    {
        if ($anime->delete()) {
            return Redirect::route('admin.animes.index')->with('success', 'Anime Deleted successfully!');
        }

        return Redirect::route('admin.animes.index')->with('error', 'Anime has not been deleted!');
    }

    public function toggleStatus(Anime $anime)
    {
        try {
            $anime->toggleStatus();

            return redirect()->back()->with('success', 'Anime status updated: '.$anime->id);
        } catch (\Throwable $th) {
            return redirect(route('admin.animes.index'))->with('error', $th->getMessage());
        }
    }

    // ──────────────────────────────────────────────
    //  AniList Integration
    // ──────────────────────────────────────────────

    public function searchInAnilist(Request $request)
    {
        $breadcrumb = [
            ['name' => 'Animes', 'url' => route('admin.animes.index')],
            ['name' => 'Search Animes', 'url' => ''],
        ];

        $q = $request->q;
        $variables = [
            'search' => $q,
            'format_in' => $request->type,
        ];

        $response = $this->httpClient()->post(config('services.anilist.graphql_url', 'https://graphql.anilist.co'), [
            'json' => [
                'query' => $this->buildGraphQLQuerySearch(),
                'variables' => $variables,
            ],
        ]);

        $json = json_decode($response->getBody()->__toString());
        $animes = $json->data->Page->media;

        return view('admin.animes.select', compact('animes', 'breadcrumb', 'q'));
    }

    public function getById($anilist_id)
    {
        $json = $this->fetchAnilistById($anilist_id);

        $data[] = $json->data->Media;
        $this->generateMassive($data);

        return redirect(route('admin.animes.index'))->with('success', 'Single anime created successfully');
    }

    public function syncAllFromAnilist()
    {
        $animes = Anime::whereNotNull('anilist_id')->get();

        foreach ($animes as $anime) {
            \App\Jobs\SyncAnimeAnilistJob::dispatch($anime->id);
        }

        return redirect(route('admin.animes.index'))->with('success', 'All animes synchronization queued to run in the background');
    }

    public function forceUpdateSilently(Anime $anime)
    {
        try {
            $json = $this->fetchAnilistById($anime->anilist_id);
            $this->updateAnimeFromAnilistData($anime, $json->data->Media);
        } catch (\Throwable $th) {
            Log::error("Sync failed for anime {$anime->id} ({$anime->title}): ".$th->getMessage());
        }
    }

    public function forceUpdate(Anime $anime)
    {
        try {
            $json = $this->fetchAnilistById($anime->anilist_id);
            $this->updateAnimeFromAnilistData($anime, $json->data->Media);

            return redirect(route('admin.animes.show', $anime->id))->with('success', 'Anime updated');
        } catch (\Throwable $th) {
            return redirect(route('admin.animes.show', $anime->id))->with('error', $th->getMessage());
        }
    }

    public function getSeasonalAnimes(Request $request)
    {
        $variables = [
            'year' => $request->year ?? now()->year,
            'season' => $request->season ?? $this->assignSeason(now()->month),
            'page' => $request->page ?? 1,
            'perPage' => $request->perPage ?? 50,
            'format_in' => $request->type ?? ['TV', 'TV_SHORT', 'ONA'],
        ];

        $response = $this->httpClient()->post(config('services.anilist.graphql_url', 'https://graphql.anilist.co'), [
            'json' => [
                'query' => $this->buildGraphQLQuerySeasonal(),
                'variables' => $variables,
            ],
        ]);

        $json = json_decode($response->getBody()->__toString());
        $animes = $json->data->Page->media;

        $this->generateMassive($animes);

        return redirect(route('admin.animes.index'))->with('success', 'Seasonal animes imported successfully');
    }

    public function wipeAnimes()
    {
        Anime::each(function ($anime) {
            $anime->delete();
        }, 100);

        Storage::disk()->delete(Storage::disk()->files('thumbnails'));
        Storage::disk()->delete(Storage::disk()->files('anime_banner'));

        return redirect(route('admin.animes.index'))->with('success', 'All animes deleted');
    }

    // ──────────────────────────────────────────────
    //  Dashboard
    // ──────────────────────────────────────────────

    public function dashboard()
    {
        $last7Days = collect(range(6, 0))->map(fn ($days) => now()->subDays($days)->toDateString());

        $userGrowth = User::select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('count', 'date');

        $trendingSongs = Song::select('songs.*')
            ->join('daily_metrics', 'songs.id', '=', 'daily_metrics.song_id')
            ->where('daily_metrics.date', '>=', now()->subDays(7))
            ->selectRaw('SUM(daily_metrics.views_count) as recent_views')
            ->groupBy('songs.id')
            ->orderByDesc('recent_views')
            ->limit(5)
            ->get();

        $stats = [
            'total_users' => User::count(),
            'active_users_24h' => User::where('last_login_at', '>=', now()->subDays(1))->count(),
            'total_songs' => Song::count(),
            'total_animes' => Anime::count(),
            'total_views' => Song::sum('views'),
        ];

        $viewsData = DailyMetric::select('date', DB::raw('SUM(views_count) as total_views'))
            ->where('date', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('total_views', 'date');

        $chartData = $last7Days->mapWithKeys(fn ($date) => [$date => $viewsData->get($date, 0)]);

        return view('admin.dashboard', compact('stats', 'chartData', 'trendingSongs', 'userGrowth'));
    }

    // ──────────────────────────────────────────────
    //  Ranking
    // ──────────────────────────────────────────────

    public function trackRanking()
    {
        try {
            Artisan::call('app:track-ranking');

            return redirect()->back()->with('success', 'Ranking tracking executed successfully.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Error executing ranking tracking: '.$th->getMessage());
        }
    }

    public function trackSeasonalRanking()
    {
        try {
            Artisan::call('app:track-ranking', ['--seasonal-only' => true]);

            return redirect()->back()->with('success', 'Seasonal ranking recalculated successfully.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Error executing seasonal ranking: '.$th->getMessage());
        }
    }

    // ══════════════════════════════════════════════
    //  PRIVATE HELPERS
    // ══════════════════════════════════════════════

    /**
     * Fetch anime data from AniList by ID.
     */
    private function fetchAnilistById(int $anilistId): object
    {
        $response = $this->httpClient()->post(config('services.anilist.graphql_url', 'https://graphql.anilist.co'), [
            'json' => [
                'query' => $this->buildGraphQLQueryId(),
                'variables' => ['id' => $anilistId],
            ],
        ]);

        return json_decode($response->getBody()->__toString());
    }

    /**
     * Update an Anime from AniList API data (studios, producers, genres, images, season, year).
     */
    private function updateAnimeFromAnilistData(Anime $anime, object $item): void
    {
        $anime->description = $item->description;
        $anime->anilist_id = $item->id;

        // Studios & Producers
        $idStudios = [];
        $idProducers = [];
        foreach ($item->studios->nodes as $node) {
            if ($node->isAnimationStudio) {
                $studio = Studio::firstOrCreate(
                    ['slug' => Str::slug($node->name)],
                    ['name' => $node->name, 'slug' => Str::slug($node->name)]
                );
                $idStudios[] = $studio->id;
            } else {
                $producer = Producer::firstOrCreate(
                    ['slug' => Str::slug($node->name)],
                    ['name' => $node->name, 'slug' => Str::slug($node->name)]
                );
                $idProducers[] = $producer->id;
            }
        }

        // External Links
        $idLinks = [];
        foreach ($item->externalLinks as $link) {
            $externalLink = ExternalLink::firstOrCreate(
                ['url' => $link->url],
                ['icon' => $link->icon, 'name' => $link->site, 'type' => $link->type, 'url' => $link->url]
            );
            $idLinks[] = $externalLink->id;
        }

        // Format
        $format = Format::firstOrCreate(
            ['slug' => Str::slug($item->format)],
            ['name' => $item->format, 'slug' => Str::slug($item->format)]
        );
        $anime->format()->associate($format);

        // Season & Year
        if (! empty($item->season) && ! empty($item->seasonYear)) {
            $anime->season_id = Season::firstOrCreate(['name' => $item->season])->id;
            $anime->year_id = Year::firstOrCreate(['name' => $item->seasonYear])->id;
        } elseif (isset($item->startDate->month)) {
            $anime->season_id = Season::firstOrCreate(['name' => $this->assignSeason($item->startDate->month)])->id;
            $anime->year_id = Year::firstOrCreate(['name' => $item->startDate->year])->id;
        }

        // Save FIRST so $anime->id exists for image relationships
        if ($anime->save()) {
            $anime->studios()->sync($idStudios);
            $anime->producers()->sync($idProducers);
            $anime->externalLinks()->sync($idLinks);

            // Images (require saved anime for imageable_id)
            $this->downloadAndStoreAnilistImage($item->bannerImage, $anime, 'anime_banner', 'banner');
            $this->downloadAndStoreAnilistImage($item->coverImage->extraLarge ?? null, $anime, 'thumbnails', 'cover');

            // Genres
            if (! empty($item->genres)) {
                $genreIds = [];
                foreach ($item->genres as $genreName) {
                    $genre = Genre::firstOrCreate(
                        ['slug' => Str::slug($genreName)],
                        ['name' => $genreName, 'slug' => Str::slug($genreName)]
                    );
                    $genreIds[] = $genre->id;
                }
                $anime->genres()->sync($genreIds);
            }
        }
    }

    /**
     * Bulk import anime from AniList data array.
     */
    private function generateMassive(array $data): void
    {
        foreach ($data as $item) {
            if (Anime::where('title', $item->title->romaji)->exists()) {
                continue;
            }

            $anime = new Anime;
            $anime->title = $item->title->romaji;
            $anime->slug = Str::slug($anime->title);
            $anime->status = true;

            $this->updateAnimeFromAnilistData($anime, $item);
        }
    }

    /**
     * Download an image from a URL and store it locally.
     * Unified handler for both thumbnails and banners from AniList.
     */
    private function downloadAndStoreAnilistImage(?string $imageUrl, Anime $anime, string $directory, string $type): void
    {
        if (! $imageUrl) {
            return;
        }

        $response = $this->httpClient()->get($imageUrl);
        $imageContent = $response->getBody()->getContents();

        if (extension_loaded('gd')) {
            $imageContent = Image::make($imageContent)->encode('webp', 100);
            $file_name = Str::slug($anime->slug).'-'.time().'.webp';
        } else {
            $contentType = $response->getHeaders()['Content-Type'][0] ?? 'image/jpeg';
            $extension = $this->mimeToExtension($contentType);
            $file_name = Str::slug($anime->slug).'-'.time().'.'.$extension;
        }

        $path = $directory.'/'.$file_name;
        Storage::disk()->put($path, $imageContent);

        if ($type === 'cover') {
            if ($anime->cover && Storage::disk()->exists($anime->cover)) Storage::disk()->delete($anime->cover);
            $anime->cover = $path;
            $anime->save();
        } elseif ($type === 'banner') {
            if ($anime->banner && Storage::disk()->exists($anime->banner)) Storage::disk()->delete($anime->banner);
            $anime->banner = $path;
            $anime->save();
        }
    }

    /**
     * Handle image uploads (file or URL) for thumbnails and banners.
     */
    private function storeAnimeImages(Anime $anime, Request $request): void
    {
        // Thumbnail
        $this->processImageUpload($anime, $request, 'file', 'thumbnail_src', 'thumbnails', 'cover');

        // Banner
        $this->processImageUpload($anime, $request, 'banner', 'banner_src', 'anime_banner', 'banner');
    }

    /**
     * Process a single image upload — from file or URL.
     */
    private function processImageUpload(Anime $anime, Request $request, string $fileField, string $urlField, string $directory, string $imageType): void
    {
        $disk = env('FILESYSTEM_DISK', 'public');

        if ($request->hasFile($fileField)) {
            // Validate
            $validator = Validator::make($request->all(), [
                $fileField => 'mimes:png,jpg,jpeg,webp|max:2048',
            ]);

            if ($validator->fails()) {
                $request->flash();
                abort(redirect()->back()->with('error', $validator->getMessageBag()));
            }

            $uploadedFile = $request->file($fileField);

            if (extension_loaded('gd')) {
                $file_name = Str::slug($request->title).'-'.time().'.webp';
                $imageContent = Image::make($uploadedFile)->encode('webp', 100);
            } else {
                $extension = $uploadedFile->extension();
                $file_name = Str::slug($request->title).'-'.time().'.'.$extension;
                $imageContent = file_get_contents($uploadedFile->getRealPath());
            }

            $path = $directory.'/'.$file_name;
            Storage::disk($disk)->put($path, $imageContent);
            
            if ($imageType === 'cover') {
                if ($anime->cover && !filter_var($anime->cover, FILTER_VALIDATE_URL)) {
                    Storage::disk($disk)->delete($anime->cover);
                }
                $anime->cover = $path;
                $anime->save();
            } elseif ($imageType === 'banner') {
                if ($anime->banner && !filter_var($anime->banner, FILTER_VALIDATE_URL)) {
                    Storage::disk($disk)->delete($anime->banner);
                }
                $anime->banner = $path;
                $anime->save();
            }

        } elseif ($request->filled($urlField)) {
            $url = $request->input($urlField);
            
            if ($imageType === 'cover') {
                if ($anime->cover && !filter_var($anime->cover, FILTER_VALIDATE_URL)) {
                    Storage::disk($disk)->delete($anime->cover);
                }
                $anime->cover = $url;
                $anime->save();
            } elseif ($imageType === 'banner') {
                if ($anime->banner && !filter_var($anime->banner, FILTER_VALIDATE_URL)) {
                    Storage::disk($disk)->delete($anime->banner);
                }
                $anime->banner = $url;
                $anime->save();
            }
        }
    }

    /**
     * Determine the anime status based on the user's role.
     */
    private function resolveAnimeStatus(Request $request): bool
    {
        $user = Auth::user();

        if ($user->hasRole('creator')) {
            return false;
        }

        if ($user->hasRole('admin') || $user->hasRole('editor')) {
            return (bool) $request->animeStatus;
        }

        return false;
    }

    /**
     * Map a MIME type to a file extension.
     */
    private function mimeToExtension(string $contentType): string
    {
        return match ($contentType) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
            default => 'bin',
        };
    }

    /**
     * Assign a season name based on the month.
     */
    private function assignSeason(int $month): string
    {
        return match (true) {
            in_array($month, [12, 1, 2]) => 'WINTER',
            in_array($month, [3, 4, 5]) => 'SPRING',
            in_array($month, [6, 7, 8]) => 'SUMMER',
            default => 'FALL',
        };
    }

    /**
     * Static data for song types dropdown.
     */
    private static function songTypes(): array
    {
        return [
            ['name' => 'Opening', 'value' => 'OP'],
            ['name' => 'Ending', 'value' => 'ED'],
            ['name' => 'Insert', 'value' => 'INS'],
            ['name' => 'Other', 'value' => 'OTH'],
        ];
    }

    /**
     * Static data for anime status dropdown.
     */
    private static function animeStatuses(): array
    {
        return [
            ['name' => 'Staged', 'value' => false],
            ['name' => 'Published', 'value' => true],
        ];
    }

    // ──────────────────────────────────────────────
    //  GraphQL Queries
    // ──────────────────────────────────────────────

    private function buildGraphQLQuerySearch(): string
    {
        return '
            query ($search: String, $format_in: [MediaFormat]) {
                Page {
                    media (search: $search, type: ANIME, format_in: $format_in) {
                        id
                        title {
                            romaji
                            english
                            native
                        }
                        coverImage {
                            extraLarge
                        }
                        bannerImage
                    }
                }
            }
        ';
    }

    private function buildGraphQLQuerySeasonal(): string
    {
        return '
            query ($year: Int, $season: MediaSeason, $page: Int, $perPage: Int, $format_in: [MediaFormat]) {
                Page (page: $page, perPage: $perPage) {
                    pageInfo {
                        total
                        perPage
                        currentPage
                        lastPage
                        hasNextPage
                    }
                    media (
                        seasonYear: $year,
                        season: $season,
                        type: ANIME,
                        format_in: $format_in,
                        isAdult: false
                    ) {
                        id
                        title { romaji english native }
                        description
                        season
                        seasonYear
                        averageScore
                        format
                        genres
                        studios { nodes { name isAnimationStudio } }
                        coverImage { large extraLarge }
                        bannerImage
                        trailer { id site thumbnail }
                        externalLinks { icon type site url }
                        startDate { month year }
                        synonyms
                    }
                }
            }';
    }

    private function buildGraphQLQueryId(): string
    {
        return '
            query ($id: Int) {
                Media (id: $id, type: ANIME) {
                    id
                    title { romaji english native }
                    description
                    season
                    seasonYear
                    format
                    genres
                    averageScore
                    studios { nodes { name isAnimationStudio } }
                    coverImage { large extraLarge }
                    bannerImage
                    episodes
                    trailer { id site thumbnail }
                    externalLinks { icon type site url }
                    startDate { month year }
                    synonyms
                }
            }
        ';
    }
}
