<?php

use App\Http\Controllers\Web\AudioController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AuthController;

use App\Http\Controllers\Web\BranchController;
use App\Http\Controllers\Web\BusinessController;
use App\Http\Controllers\Web\ServiceController;

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

Route::get('/myAppointments', fn () => view('pages.myAppointments'));

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
        // Single page index — still comes from BusinessController
        Route::get('/', [BusinessController::class, 'index'])->name('test.index');

        // Individual POST actions handled by their own controllers
        Route::post('/business', [BusinessController::class, 'store'])->name('test.business.store');
        Route::post('/branch', [BranchController::class, 'store'])->name('test.branch.store');
        Route::post('/service', [ServiceController::class, 'store'])->name('test.service.store');
    });
});
