<?php

use Illuminate\Support\Facades\Route;

// Controllers
use App\Http\Controllers\Web\Auth\AuthController;
use App\Http\Controllers\Web\Business\PrivateBusinessController;
use App\Http\Controllers\Web\Business\BusinessAssignmentController;
use App\Http\Controllers\Web\Branch\PrivateBranchController;
use App\Http\Controllers\Web\Service\PrivateServiceController;
use App\Http\Controllers\Web\NotificationController;
use App\Http\Controllers\Web\Rule\RuleController;
use App\Http\Controllers\Web\SearchController;
use App\Http\Controllers\Web\Asset\AssetController;
use App\Http\Controllers\Web\Appointment\AppointmentController;

// Chatbot
use App\Http\Controllers\Web\ChatbotController;

/**
 * Public
 */
Route::view('/', 'pages.welcome')->name('home');
Route::view('/dev', 'pages.dev')->name('dev');

Route::prefix('search')
    ->controller(SearchController::class)
    ->group(function () {
        Route::get('/', 'index')->name('manualBooking.index');
        Route::get('/services', 'index')->name('public.services.index');
        Route::get('/locations', 'index')->name('public.branches.index');

        Route::get('/{id}', 'show')->name('manualBooking.show');
    });

Route::prefix('business')->controller(PrivateBusinessController::class)->group(function () {
    Route::get('/{businessId}/book', [PrivateBusinessController::class, 'book'])->name('business.book');
});

Route::prefix('service')->controller(PrivateServiceController::class)->group(function () {
    Route::get('/{serviceId}/book', [PrivateServiceController::class, 'book'])->name('service.book');
});

Route::prefix('service')->group(function () {
    Route::get('/{serviceId}/asset/{assetId}/book', [AssetController::class, 'book'])->name('asset.book');
});

/**
 * Guest
 */
Route::middleware('guest')
    ->controller(AuthController::class)
    ->group(function () {
        Route::get('/login', 'showAuth')->name('login');
        Route::get('/register', 'showAuth')->name('register');
        Route::post('/login', 'login')->name('login.submit');
        Route::post('/register', 'register')->name('register.submit');
    });

/**
 * Protected
 */
Route::middleware(['auth'])->group(function () {
    // User
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', fn() => view('pages.private.dashboard'))->name('dashboard');
    Route::get('/my-appointments', fn() => view('pages.myAppointments'))->name('myAppointments');
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    // Notifications
    Route::prefix('notifications')
        ->name('notifications.')
        ->controller(NotificationController::class)
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/mark-all-read', 'markAllRead')->name('markAllRead');
            Route::post('/{id}/dismiss', 'dismiss')->name('dismiss');
            Route::post('/{id}/read', 'markAsRead')->name('markRead');
        });

    /**
     * Owner/Admin Management Area
     */
    Route::prefix('businesses')->group(function () {
        Route::get('/', [PrivateBusinessController::class, 'index'])->name('business.index');
        Route::post('/', [PrivateBusinessController::class, 'store'])->name('business.store');
        Route::get('/{businessId}', [PrivateBusinessController::class, 'show'])->name('business.show');
        Route::put('/{businessId}', [PrivateBusinessController::class, 'update'])->name('business.update');
        Route::delete('/{businessId}', [PrivateBusinessController::class, 'delete'])->name('business.delete');
        Route::post('/{businessId}/restore', [PrivateBusinessController::class, 'restore'])->name('business.restore');

        Route::controller(BusinessAssignmentController::class)->group(function () {
            Route::post('/{businessId}/assign', 'store')->name('business.assign');
            Route::patch('/{businessId}/users/{user}', 'update')->name('business.users.update');
            Route::delete('/{businessId}/users/{user}', 'delete')->name('business.users.delete');
        });
    });

    Route::prefix('branches')
        ->name('branch.')
        ->controller(PrivateBranchController::class)
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'store')->name('store');
            Route::get('/{branchId}', 'show')->name('show');
            Route::put('/{branchId}', 'update')->name('update');
            Route::delete('/{branchId}', 'delete')->name('delete');
            Route::post('/{branchId}/restore', 'restore')->name('restore');
        });

    Route::prefix('services')
        ->name('service.')
        ->controller(PrivateServiceController::class)
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'store')->name('store');
            Route::get('/{serviceId}', 'show')->name('show');
            Route::put('/{serviceId}', 'update')->name('update');
            Route::delete('/{serviceId}', 'delete')->name('delete');
            Route::post('/{serviceId}/restore', 'restore')->name('restore');
        });

    Route::prefix('assets')
        ->name('asset.')
        ->controller(AssetController::class)
        ->group(function () {
            Route::get('/', 'index')->name('index'); // Create
            Route::post('/', 'store')->name('store'); // Create
            Route::get('/{assetId}', 'show')->name('show');
            Route::put('/{assetId}', 'update')->name('update'); // Update
            Route::delete('/{assetId}', 'delete')->name('delete'); // Delete
            Route::post('/{assetId}/restore', 'restore')->name('restore'); // Restore soft-deleted
        });

    Route::get('/chatbot', [ChatbotController::class, 'index']);

    Route::prefix('rules')
        ->name('rule.')
        ->controller(RuleController::class)
        ->group(function () {
            Route::post('/', 'store')->name('store'); // Create
            Route::put('/{ruleId}', 'update')->name('update'); // Update
            Route::delete('/{ruleId}', 'delete')->name('delete'); // Delete
            Route::post('/{ruleId}/reorder',  'reorder')->name('reorder');
        });

    Route::prefix('appointments')
        ->name('appointment.')
        ->controller(AppointmentController::class)
        ->group(function () {
            Route::get('/slots', 'slots')->name('slots');   // GET  /appointments/slots?...
            Route::post('/', 'store')->name('store');        // POST /appointments
        });
});
