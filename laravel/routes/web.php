<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\Asset\AssetController;
use App\Http\Controllers\Web\Auth\AuthController;
use App\Http\Controllers\Web\Business\BusinessController;
use App\Http\Controllers\Web\Business\PublicBusinessController;
use App\Http\Controllers\Web\Branch\BranchController;
use App\Http\Controllers\Web\Branch\PublicBranchController;
use App\Http\Controllers\Web\Service\PublicServiceController;
use App\Http\Controllers\Web\Service\ServiceController;
use App\Http\Controllers\Web\Rule\RuleController;

/**
 * Public Routes
 */
Route::view('/', 'pages.welcome')->name('home');
Route::view('dev', 'pages.dev')->name('dev');

Route::prefix('manual-booking')->group(function () {
    Route::get('/services', [PublicServiceController::class, 'index'])->name('public.services.index');
    Route::get('/locations', [PublicBranchController::class, 'index'])->name('public.branches.index');

    Route::get('/', [PublicBusinessController::class, 'index'])->name('manualBooking.index');
    Route::get('/{id}', [PublicBusinessController::class, 'show'])->name('manualBooking.show');
});

/**
 * Guest Routes (Authentication)
 */
Route::controller(AuthController::class)
    ->middleware('guest')
    ->group(function () {
        Route::get('/login', 'showAuth')->name('login');
        Route::get('/register', 'showAuth')->name('register');
        Route::post('/login', 'login')->name('login.submit');
        Route::post('/register', 'register')->name('register.submit');
    });

/**
 * Protected Routes
 */
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', fn() => view('pages.private.dashboard'))->name('dashboard');
    Route::get('/my-appointments', fn() => view('pages.myAppointments'))->name('myAppointments');
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    /**
     * Owner/Admin Management Area
     */
    Route::prefix('my-businesses')->group(function () {
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

        // Asset
        Route::prefix('asset')->name('asset.')->controller(AssetController::class)->group(function () {
            Route::post('/', 'store')->name('store'); // Create
            Route::put('/{serviceId}', 'update')->name('update'); // Update
            Route::delete('/{serviceId}', 'delete')->name('delete'); // Delete
            Route::post('/{serviceId}/restore', 'restore')->name('restore'); // Restore soft-deleted
        });

        // Rule
        Route::prefix('rule')->name('rule.')->controller(RuleController::class)->group(function () {
            Route::post('/', 'store')->name('store'); // Create
            Route::put('/{serviceId}', 'update')->name('update'); // Update
            Route::delete('/{serviceId}', 'delete')->name('delete'); // Delete
            Route::post('/{serviceId}/restore', 'restore')->name('restore'); // Restore soft-deleted
        });
    });
});
