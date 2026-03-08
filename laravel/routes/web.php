<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\BranchController;
use App\Http\Controllers\Web\BusinessController;
use App\Http\Controllers\Web\ServiceController;

/**
 * Public Routes
 */
Route::view('/', 'pages.welcome')->name('home');
Route::view('dev', 'pages.dev')->name('dev');
Route::view('myAppointments', 'pages.myAppointments')->name('myAppointments');

Route::controller(AuthController::class)->middleware('guest')->group(function () {
    // Show forms
    Route::get('/login', 'showAuth')->name('login');
    Route::get('/register', 'showAuth')->name('register');

    // Handle submission
    Route::post('/login', 'login')->name('login.submit');
    Route::post('/register', 'register')->name('register.submit');
});

/**
 * Protected Routes
 */
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', fn() => view('pages.dashboard'))->name('dashboard');
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    
    /**
     * Businesses, Branches, Services Routes
     */
    Route::prefix('businesses')
        ->middleware(['auth'])
        ->group(function () {
            Route::get('/', [BusinessController::class, 'index'])->name('business.index');
            Route::post('/', [BusinessController::class, 'store'])->name('business.store');

            Route::prefix('{businessId}')->group(function () {
                Route::get('/', [BusinessController::class, 'show'])->name('business.show');
                Route::put('/', [BusinessController::class, 'update'])->name('business.update');
                Route::delete('/', [BusinessController::class, 'delete'])->name('business.delete');
                Route::post('/restore', [BusinessController::class, 'restore'])->name('business.restore');

                Route::prefix('branches')
                    ->name('branch.')
                    ->controller(BranchController::class)
                    ->group(function () {
                        Route::post('/', 'store')->name('store');
                        Route::put('/{branchId}', 'update')->name('update');
                        Route::delete('/{branchId}', 'delete')->name('delete');
                        Route::post('/{branchId}/restore', 'restore')->name('restore');
                    });

                Route::prefix('services')
                    ->name('service.')
                    ->controller(ServiceController::class)
                    ->group(function () {
                        Route::post('/', 'store')->name('store');
                        Route::put('/{serviceId}', 'update')->name('update');
                        Route::delete('/{serviceId}', 'delete')->name('delete');
                        Route::post('/{serviceId}/restore', 'restore')->name('restore');
                    });
            });
        });
});
