<?php

use App\Http\Controllers\Web\AudioController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AuthController;


/**
 * Audio test
 */
Route::get('/audio', [AudioController::class, 'index']);
Route::post('/audio/transcribe', [AudioController::class, 'upload']);
//Route::post('/audio/chunk', [WebAudioController::class, 'uploadChunk']);
//Route::get('/audio/transcript/{session}', [WebAudioController::class, 'getTranscript']);

/**
 * Public routes
 */
Route::get('/', fn () => view('pages.welcome'));

Route::get('/dev', fn () => view('pages.dev'));

Route::get('/auth', fn () => view('pages.auth'));

/**
 * Authentication routes
 */
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

/**
 * Protected routes
 */
Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', fn () => view('pages.dashboard'))
        ->name('dashboard');

    Route::middleware('role:client')->get('/client', fn () => view('client'));

    Route::middleware('role:provider')->get('/provider', fn () => view('provider'));

    Route::middleware('role:admin')->get('/admin', fn () => view('admin'));
});
