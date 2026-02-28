<?php

use App\Http\Controllers\Admin\AnimeController as AdminAnimeController;
use App\Http\Controllers\Admin\ArtistController as AdminArtistController;
use App\Http\Controllers\Admin\BadgeController as AdminBadgeController;
use App\Http\Controllers\Admin\CommentController as AdminCommentController;
use App\Http\Controllers\Admin\ProducerController as AdminProducerController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\RoleController as AdminRoleController;
use App\Http\Controllers\Admin\SeasonController as AdminSeasonController;
use App\Http\Controllers\Admin\SongController as AdminSongController;
use App\Http\Controllers\Admin\SongVariantController as AdminSongVariantController;
use App\Http\Controllers\Admin\StudioController as AdminStudioController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\UserRequestController as AdminUserRequestController;
use App\Http\Controllers\Admin\VideoController as AdminVideoController;
use App\Http\Controllers\Admin\YearController as AdminYearController;
use App\Http\Controllers\AnimeController;
use App\Http\Controllers\ArtistController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PlaylistController;
use App\Http\Controllers\ProducerController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SongController;
use App\Http\Controllers\SongVariantController;
use App\Http\Controllers\StudioController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserRequestController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::controller(AnimeController::class)->group(function () {
    Route::get('/', 'index')->name('home');

    Route::get('/animes', 'animes')->name('animes.index');
    Route::get('/anime/{anime:slug}', 'show')->name('animes.show');
});

Route::controller(SongController::class)->group(function () {
    Route::get('/songs', 'index')->name('songs.index');
    Route::get('/songs/seasonal', 'seasonal')->name('songs.seasonal');
    Route::get('/songs/ranking', 'ranking')->name('songs.ranking');
    Route::get('/song/{anime:slug}/{song:slug}', 'showAnimeSong')->name('songs.show.nested')->scopeBindings();
});

Route::controller(UserController::class)->group(function () {
    Route::get('/users/{user:slug}', 'show')->name('users.show');
});

Route::controller(ArtistController::class)->group(function () {
    Route::get('/artists/{artist:slug}', 'show')->name('artists.show');
    Route::get('/artists', 'index')->name('artists.index');
});

Route::controller(StudioController::class)->group(function () {
    Route::get('/studios/{studio:slug}', 'show')->name('studios.show');
    Route::get('/studios', 'index')->name('studios.index');
});

Route::controller(ProducerController::class)->group(function () {
    Route::get('/producers/{producer:slug}', 'show')->name('producers.show');
    Route::get('/producers', 'index')->name('producers.index');
});

// Resources
// Only write methods — index/show are handled by the manual slug routes above
Route::resource('animes', AnimeController::class)->only(['store', 'update', 'destroy']);
Route::resource('songs', SongController::class)->only(['store', 'update', 'destroy']);
Route::resource('variants', SongVariantController::class);
Route::resource('requests', UserRequestController::class);
Route::resource('reports', ReportController::class);
Route::resource('playlists', PlaylistController::class);

/*
|--------------------------------------------------------------------------
| Admin Routes (Staff Middleware)
|--------------------------------------------------------------------------
*/

Route::middleware('role:admin,editor,creator')->prefix('admin')->as('admin.')->group(function () {
    Route::get('/dashboard', [AdminAnimeController::class, 'dashboard'])->name('dashboard');

    // Songs & Variants
    Route::get('songs/latest-number', [AdminSongController::class, 'getLatestNumber'])->name('songs.latest_number');
    Route::resource('songs', AdminSongController::class);

    Route::resource('variants', AdminSongVariantController::class);

    // Common Resources
    Route::resource('videos', AdminVideoController::class);
    Route::patch('/requests/{request}/attend', [AdminUserRequestController::class, 'attend'])->name('requests.attend');
    Route::resource('requests', AdminUserRequestController::class);
    Route::resource('comments', AdminCommentController::class);
    Route::resource('studios', AdminStudioController::class);
    Route::resource('producers', AdminProducerController::class);

    // Reports
    Route::patch('/reports/{report}/toggle', [AdminReportController::class, 'toggle'])->name('reports.toggle');
    Route::resource('reports', AdminReportController::class);

    // Animes
    Route::controller(AdminAnimeController::class)->prefix('animes')->as('animes.')->group(function () {
        Route::get('/autocomplete', 'autocomplete')->name('autocomplete');
        Route::patch('/{anime}/toggle-status', 'toggleStatus')->name('toggle.status');
        Route::post('/search-animes', 'searchInAnilist')->name('search.animes');
        Route::get('/by-id/{id}', 'getById')->name('by.id');
        Route::post('/seasonal-animes', 'getSeasonalAnimes')->name('seasonal.animes');
        Route::get('/{anime}/force-update', 'forceUpdate')->name('force.update');
        Route::post('/sync-all', 'syncAllFromAnilist')->name('sync.all');
        Route::delete('/wipe', 'wipeAnimes')->name('wipe');
        Route::post('/track-ranking', 'trackRanking')->name('track.ranking');
        Route::post('/track-seasonal-ranking', 'trackSeasonalRanking')->name('track.seasonal.ranking');
    });
    Route::resource('animes', AdminAnimeController::class);

    // Artists
    Route::resource('artists', AdminArtistController::class);

    // Users
    Route::resource('users', AdminUserController::class);

    // Years
    Route::patch('years/{year}/set-current', [AdminYearController::class, 'setCurrent'])->name('years.set.current');
    Route::resource('years', AdminYearController::class);

    // Seasons
    Route::patch('seasons/{season}/set-current', [AdminSeasonController::class, 'setCurrent'])->name('seasons.set.current');
    Route::resource('seasons', AdminSeasonController::class);

    // Roles
    Route::resource('roles', AdminRoleController::class);

    // Badges
    Route::resource('badges', AdminBadgeController::class);
});

/*
|--------------------------------------------------------------------------
| Auth & Interaction Routes
|--------------------------------------------------------------------------
*/

Auth::routes();

// Authenticated Routes
Route::middleware('auth')->group(function () {

    // Profile Management
    Route::controller(UserController::class)->group(function () {
        Route::post('/profile/score-format', 'changeScoreFormat')->name('profile.score-format');
        Route::post('/profile/avatar', 'uploadProfilePic')->name('profile.avatar');
        Route::post('/profile/banner', 'uploadBannerPic')->name('profile.banner');
        Route::get('/settings', 'settings')->name('users.settings');
        Route::get('/{user:slug}/favorites', 'favorites')->name('users.favorites');
    });

    // Song Variants Interactions
    Route::controller(SongVariantController::class)->prefix('variants')->as('variants.')->group(function () {
        Route::post('/{variant}/rate', 'rate')->name('rate');
        Route::post('/{variant}/like', 'like')->name('like');
        Route::post('/{variant}/dislike', 'dislike')->name('dislike');
        Route::post('/{variant}/favorite', 'toggleFavorite')->name('favorite');
    });

    // Comments
    Route::controller(CommentController::class)->prefix('comments')->as('comments.')->group(function () {
        Route::post('/{comment}/like', 'like')->name('like');
        Route::post('/{comment}/dislike', 'dislike')->name('dislike');
        Route::post('/{comment}/reply', 'reply')->name('reply');
    });
    Route::resource('comments', CommentController::class);
});
