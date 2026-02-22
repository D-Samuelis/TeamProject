<?php

use App\Http\Controllers\Web\AudioController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AuthController;

use App\Http\Controllers\TestController;

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
Route::get('/', fn() => view('pages.welcome'));

Route::get('/dev', fn() => view('pages.dev'));

Route::get('/auth', fn() => view('pages.auth'));

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
    Route::get('/dashboard', fn() => view('pages.dashboard'))->name('dashboard');

    Route::middleware('role:client')->get('/client', fn() => view('client'));

    Route::middleware('role:provider')->get('/provider', fn() => view('provider'));

    Route::middleware('role:admin')->get('/admin', fn() => view('admin'));

    Route::prefix('test-admin')->group(function () {
        Route::get('/', [TestController::class, 'index'])->name('test.index');

        Route::post('/business', [TestController::class, 'storeBusiness'])->name('test.business.store');
        Route::post('/branch', [TestController::class, 'storeBranch'])->name('test.branch.store');
        Route::post('/service', [TestController::class, 'storeService'])->name('test.service.store');
        Route::post('/asset', [TestController::class, 'storeAsset'])->name('test.asset.store');
        Route::post('/attach-asset', [TestController::class, 'attachAsset'])->name('test.asset.attach');
    });
});
