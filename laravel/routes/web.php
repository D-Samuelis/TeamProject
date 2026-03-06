<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\BranchController;
use App\Http\Controllers\Web\BusinessController;
use App\Http\Controllers\Web\ServiceController;

/**
 * Public Routes
 */
Route::prefix('/')->group(function () {
    Route::view('/', 'pages.welcome')->name('home');
    Route::view('dev', 'pages.dev');

    Route::controller(AuthController::class)->group(function () {
        Route::get('/login', [AuthController::class, 'showAuth'])->name('login');
        Route::get('/register', [AuthController::class, 'showAuth'])->name('register');

        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/register', [AuthController::class, 'register']);
    });
    Route::view('myAppointments', 'pages.myAppointments');
});

/**
 * Protected Routes
 */
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', fn() => view('pages.dashboard'))->name('dashboard');

    Route::prefix('test-admin')->group(function () {
        Route::get('/', [BusinessController::class, 'index'])->name('test.index');
        Route::post('/business', [BusinessController::class, 'store'])->name('test.business.store');

        Route::delete('/business/{businessId}', [BusinessController::class, 'delete'])
            ->name('business.delete');

        Route::post('/business/{businessId}/restore', [BusinessController::class, 'restore'])
            ->name('business.restore');

        Route::post('/branch', [BranchController::class, 'store'])->name('test.branch.store');
        Route::post('/service', [ServiceController::class, 'store'])->name('test.service.store');
    });

    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
});
