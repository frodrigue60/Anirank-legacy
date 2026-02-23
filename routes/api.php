<?php

use App\Http\Controllers\Api\ArtistController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\InitController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\PlaylistController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\ProducerController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\SongController;
use App\Http\Controllers\Api\SongVariantController;
use App\Http\Controllers\Api\StudioController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\UserRequestController;
use Illuminate\Support\Facades\Route;

// Auth (Public)
Route::controller(AuthController::class)->prefix('auth')->group(function () {
    Route::post('register', 'register')->name('api.auth.register');
    Route::post('login', 'login')->name('api.auth.login');
});

// Initialization (SPA Config)
Route::get('init', [InitController::class, 'index'])->name('api.init');

// Global Search
Route::get('search', SearchController::class)->name('api.search');

// Posts / Animes
Route::controller(PostController::class)->group(function () {
    Route::get('home', 'home')->name('api.home');
    
    // Public routes (Auth handled optionally in controllers/models)
    Route::get('animes', 'index')->name('api.animes.index');
    Route::get('animes/{post:slug}', 'show')->name('api.animes.show');
    Route::get('animes/{post:slug}/songs/{song:slug}', [SongController::class, 'show'])->name('api.songs.show');
});

// Songs
Route::controller(SongController::class)->group(function () {
    Route::get('songs', 'index')->name('api.songs.index');
    Route::get('songs/seasonal', 'seasonal')->name('api.songs.seasonal');
    Route::get('songs/ranking/global', 'globalRanking')->name('api.songs.ranking.global');
    Route::get('songs/ranking/seasonal', 'seasonalRanking')->name('api.songs.ranking.seasonal');
    Route::get('songs/{song}/comments', 'comments')->name('api.songs.comments');
});

// Comments
Route::controller(CommentController::class)->group(function () {
    Route::get('comments', 'index')->name('api.comments.index');
    Route::get('comments/{comment}', 'show')->name('api.comments.show');
});

// Song Variants
Route::controller(SongVariantController::class)->group(function () {
    Route::get('variants', 'index')->name('api.variants.index');
    Route::get('variants/{variant}/video', 'video')->name('api.variants.video');
});

// Artists
Route::get('artists', [ArtistController::class, 'index'])->name('api.artists.index');
Route::get('artists/{artist:slug}', [ArtistController::class, 'show'])->name('api.artists.show');
Route::get('artists/{artist:slug}/songs', [ArtistController::class, 'songs'])->name('api.artists.songs');

// Users
Route::get('users/{user}', [UserController::class, 'show'])->name('api.users.show');

// Studios
Route::get('studios', [StudioController::class, 'index'])->name('api.studios.index');
Route::get('studios/{studio:slug}', [StudioController::class, 'show'])->name('api.studios.show');
Route::get('studios/{studio:slug}/animes', [StudioController::class, 'animes'])->name('api.studios.animes');

// Producers
Route::get('producers', [ProducerController::class, 'index'])->name('api.producers.index');
Route::get('producers/{producer:slug}', [ProducerController::class, 'show'])->name('api.producers.show');
Route::get('producers/{producer:slug}/animes', [ProducerController::class, 'animes'])->name('api.producers.animes');

// Auth Routes
Route::middleware(['auth:sanctum'])->group(function () {
    // Auth
    Route::controller(AuthController::class)->prefix('auth')->group(function () {
        Route::post('logout', 'logout')->name('api.auth.logout');
        Route::get('me', 'me')->name('api.auth.me');
    });

    // Playlists
    Route::controller(PlaylistController::class)->group(function () {
        Route::get('playlists', 'index')->name('api.playlists.index');
        Route::post('playlists', 'store')->name('api.playlists.store');
        Route::post('playlists/{playlist}/toggle-song', 'toggleSong')->name('api.playlists.toggle.song');
    });

    // Songs
    Route::controller(SongController::class)->group(function () {
        Route::post('songs/{song}/like', 'like')->name('api.songs.like');
        Route::post('songs/{song}/dislike', 'dislike')->name('api.songs.dislike');
        Route::post('songs/{song}/favorite', 'toggleFavorite')->name('api.songs.toggle.favorite');
        Route::post('songs/{song}/rate', 'rate')->name('api.songs.rate');
        Route::post('songs/comments', 'storeComment')->name('api.songs.store.comment');
    });

    // Reports
    Route::controller(ReportController::class)->group(function () {
        Route::post('reports', 'store')->name('api.reports.store');
    });

    // Comments
    Route::controller(CommentController::class)->group(function () {
        Route::post('comments/{comment}/like', 'like')->name('api.comments.like');
        Route::post('comments/{comment}/dislike', 'dislike')->name('api.comments.dislike');
        Route::post('comments/{comment}/reply', 'reply')->name('api.comments.reply');
        Route::post('comments', 'store')->name('api.comments.store');
        Route::patch('comments/{comment}', 'update')->name('api.comments.update');
        Route::delete('comments/{comment}', 'destroy')->name('api.comments.destroy');
    });

    // User Requests
    Route::resource('requests', UserRequestController::class);

    // User
    Route::controller(UserController::class)->group(function () {
        Route::post('users/avatar', 'uploadAvatar')->name('api.users.upload.avatar');
        Route::post('users/banner', 'uploadBanner')->name('api.users.upload.banner');
        Route::post('users/score-format', 'setScoreFormat')->name('api.users.score.format');
        Route::post('users/favorites', 'favorites')->name('api.users.favorites');
    });
});
