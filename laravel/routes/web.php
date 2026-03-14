<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\Asset\AssetController;

// Auth
use App\Http\Controllers\Web\Auth\AuthController;

// Business
use App\Http\Controllers\Web\Business\PrivateBusinessController;
use App\Http\Controllers\Web\Business\PublicBusinessController;
use App\Http\Controllers\Web\Business\BusinessAssignmentController;

// Branch & Service
use App\Http\Controllers\Web\Branch\BranchController;
use App\Http\Controllers\Web\Branch\PublicBranchController;
use App\Http\Controllers\Web\Service\ServiceController;
use App\Http\Controllers\Web\Service\PublicServiceController;

// Notifications
use App\Http\Controllers\Web\NotificationController;
use App\Http\Controllers\Web\Rule\RuleController;

use App\Http\Controllers\ChatbotController;

Route::get('/chatbot', [ChatbotController::class, 'index']);
Route::post('/chatbot/message', [ChatbotController::class, 'message']);

/**
 * Public
 */
Route::view('/', 'pages.welcome')->name('home');
Route::view('/dev', 'pages.dev')->name('dev');

Route::prefix('manual-booking')->group(function () {
    Route::get('/', [PublicBusinessController::class, 'index'])->name('manualBooking.index');
    Route::get('/services', [PublicServiceController::class, 'index'])->name('public.services.index');
    Route::get('/locations', [PublicBranchController::class, 'index'])->name('public.branches.index');
    Route::get('/{id}', [PublicBusinessController::class, 'show'])->name('manualBooking.show');
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

    Route::prefix('branches')->name('branch.')->controller(BranchController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
        Route::get('/{branchId}', 'show')->name('show');
        Route::put('/{branchId}', 'update')->name('update');
        Route::delete('/{branchId}', 'delete')->name('delete');
        Route::post('/{branchId}/restore', 'restore')->name('restore');
    });

    Route::prefix('services')->name('service.')->controller(ServiceController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
        Route::get('/{serviceId}', 'show')->name('show');
        Route::put('/{serviceId}', 'update')->name('update');
        Route::delete('/{serviceId}', 'delete')->name('delete');
        Route::post('/{serviceId}/restore', 'restore')->name('restore');
    });

    Route::prefix('assets')->name('asset.')->controller(AssetController::class)->group(function () {
        Route::get('/', 'index')->name('index'); // Create
        Route::post('/', 'store')->name('store'); // Create
        Route::get('/{assetId}', 'show')->name('show');
        Route::put('/{assetId}', 'update')->name('update'); // Update
        Route::delete('/{assetId}', 'delete')->name('delete'); // Delete
        Route::post('/{assetId}/restore', 'restore')->name('restore'); // Restore soft-deleted
    });

    Route::prefix('rules')->name('rule.')->controller(RuleController::class)->group(function () {
        Route::post('/', 'store')->name('store'); // Create
        Route::put('/{ruleId}', 'update')->name('update'); // Update
        Route::delete('/{ruleId}', 'delete')->name('delete'); // Delete
        Route::post('/{ruleId}/restore', 'restore')->name('restore'); // Restore soft-deleted
    });
});
