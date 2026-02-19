<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\{
    PostController,
    ArtistController,
    UserController,
    ReportController,
    CommentController,
    SongController,
    SongVariantController,
    SeasonController,
    YearController,
    StudioController,
    ProducerController,
    UserRequestController,
    PlaylistController
};

use App\Http\Controllers\Admin\{
    PostController as AdminPostController,
    ArtistController as AdminArtistController,
    UserController as AdminUserController,
    ReportController as AdminReportController,
    UserRequestController as AdminUserRequestController,
    SongController as AdminSongController,
    VideoController as AdminVideoController,
    SongVariantController as AdminSongVariantController,
    YearController as AdminYearController,
    SeasonController as AdminSeasonController,
    CommentController as AdminCommentController,
    StudioController as AdminStudioController,
    ProducerController as AdminProducerController,
    RoleController as AdminRoleController
};

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::controller(PostController::class)->group(function () {
    Route::get('/', 'index')->name('home');

    Route::get('/animes', 'animes')->name('posts.animes');
    Route::get('/anime/{post:slug}', 'show')->name('posts.show');
});

Route::controller(SongController::class)->group(function () {
    Route::get('/songs', 'index')->name('songs.index');
    Route::get('/songs/seasonal', 'seasonal')->name('songs.seasonal');
    Route::get('/songs/ranking', 'ranking')->name('songs.ranking');
    Route::get('/song/{post:slug}/{song:slug}', 'showAnimeSong')->name('songs.show.nested')->scopeBindings();
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
Route::resource('posts', PostController::class)->only(['store', 'update', 'destroy']);
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

Route::middleware('staff')->prefix('admin')->as('admin.')->group(function () {
    Route::get('/dashboard', [AdminPostController::class, 'dashboard'])->name('dashboard');

    // Songs & Variants
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

    // Posts
    Route::controller(AdminPostController::class)->prefix('posts')->as('posts.')->group(function () {
        Route::patch('/{post}/toggle-status', 'toggleStatus')->name('toggle.status');
        Route::post('/search-animes', 'searchInAnilist')->name('search.animes');
        Route::get('/by-id/{id}', 'getById')->name('by.id');
        Route::post('/seasonal-animes', 'getSeasonalAnimes')->name('seasonal.animes');
        Route::get('/{post}/force-update', 'forceUpdate')->name('force.update');
        Route::post('/sync-all', 'syncAllFromAnilist')->name('sync.all');
        Route::delete('/wipe', 'wipePosts')->name('wipe');
        Route::post('/track-ranking', 'trackRanking')->name('track.ranking');
    });
    Route::resource('posts', AdminPostController::class);

    //Artists
    Route::resource('artists', AdminArtistController::class);

    //Users
    Route::resource('users', AdminUserController::class);

    //Years
    Route::patch('years/{year}/set-current', [AdminYearController::class, 'setCurrent'])->name('years.set.current');
    Route::resource('years', AdminYearController::class);

    //Seasons
    Route::patch('seasons/{season}/set-current', [AdminSeasonController::class, 'setCurrent'])->name('seasons.set.current');
    Route::resource('seasons', AdminSeasonController::class);

    // Roles
    Route::resource('roles', AdminRoleController::class);
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
