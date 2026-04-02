<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Web\Auth\AuthController;
use App\Http\Controllers\Web\SearchController;
use App\Http\Controllers\Web\NotificationController;
use App\Http\Controllers\Web\ChatbotController;
use App\Http\Controllers\Web\Business\ManageBusinessController;
use App\Http\Controllers\Web\RoleAssignmentController;
use App\Http\Controllers\Web\Branch\ManageBranchController;
use App\Http\Controllers\Web\Service\ManageServiceController;
use App\Http\Controllers\Web\Asset\AssetController;
use App\Http\Controllers\Web\Rule\RuleController;
use App\Http\Controllers\Web\Appointment\AppointmentController;

/**
 * Public
 */
Route::view('/', 'pages.welcome')->name('home');
//Route::view('/dev', 'pages.dev')->name('dev');

Route::prefix('search')
    ->name('search.')
    ->controller(SearchController::class)
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/businesses/{businessId}', 'showBusiness')->name('business.show');
        Route::get('/branches/{branchId}', 'showBranch')->name('branch.show');
        Route::get('/services/{serviceId}', 'showService')->name('service.show');
    });

Route::prefix('business')->controller(ManageBusinessController::class)->group(function () {
    Route::get('/{businessId}/book', [ManageBusinessController::class, 'book'])->name('business.book');
});

Route::prefix('service')->controller(ManageServiceController::class)->group(function () {
    Route::get('/{serviceId}/book', [ManageServiceController::class, 'book'])->name('service.book');
});

Route::prefix('service')->group(function () {
    Route::get('/{serviceId}/asset/{assetId}/book', [AssetController::class, 'book'])->name('asset.book');
});

Route::prefix('appointments')
    ->name('appointment.')
    ->controller(AppointmentController::class)
    ->group(function () {
        Route::get('/slots', 'slots')->name('slots');
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
 * Authenticated
 */
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', fn() => view('pages.dashboard'))->name('dashboard');
    Route::get('/my-appointments', fn() => view('pages.myAppointments'))->name('myAppointments');

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

    // Appointments
    Route::prefix('appointments')
        ->name('appointment.')
        ->controller(AppointmentController::class)
        ->group(function () {
            Route::post('/', 'store')->name('store');
        });

    // AI Agent
    Route::get('/chatbot', [ChatbotController::class, 'index']);
    Route::post('/chatbot/token', [ChatbotController::class, 'issueToken']);

    /**
     * Management
     */
    Route::prefix('manage')
        ->name('manage.')
        ->group(function () {
            // Businesses
            Route::prefix('businesses')
                ->name('business.')
                ->group(function () {
                    Route::controller(ManageBusinessController::class)->group(function () {
                        Route::get('/', 'index')->name('index');
                        Route::get('/{businessId}', 'show')->name('show');
                        Route::post('/', 'store')->name('store');
                        Route::put('/{businessId}', 'update')->name('update');
                        Route::delete('/{businessId}', 'delete')->name('delete');
                        Route::patch('/{businessId}/restore', 'restore')->name('restore');
                    });

                    Route::controller(RoleAssignmentController::class)->group(function () {
                        Route::post('/{businessId}/assign', 'store')->name('assign');
                        Route::patch('/{businessId}/users/{user}', 'update')->name('users.update');
                        Route::delete('/{businessId}/users/{user}', 'delete')->name('users.delete');
                    });
                });

            // Branches
            Route::prefix('branches')
                ->name('branch.')
                ->controller(ManageBranchController::class)
                ->group(function () {
                    Route::get('/', 'index')->name('index');
                    Route::get('/{branchId}', 'show')->name('show');
                    Route::post('/', 'store')->name('store');
                    Route::put('/{branchId}', 'update')->name('update');
                    Route::delete('/{branchId}', 'delete')->name('delete');
                    Route::patch('/{branchId}/restore', 'restore')->name('restore');
                });

            // Services
            Route::prefix('services')
                ->name('service.')
                ->controller(ManageServiceController::class)
                ->group(function () {
                    Route::get('/', 'index')->name('index');
                    Route::get('/{serviceId}', 'show')->name('show');
                    Route::post('/', 'store')->name('store');
                    Route::put('/{serviceId}', 'update')->name('update');
                    Route::delete('/{serviceId}', 'delete')->name('delete');
                    Route::patch('/{serviceId}/restore', 'restore')->name('restore');
                    Route::post('/{serviceId}/branches/{branchId}/assign', 'assign')->name('branch.assign');
                    Route::delete('/{serviceId}/branches/{branchId}/unassign', 'unassign')->name('branch.unassign');
                });

            // Assets
            Route::prefix('assets')
                ->name('asset.')
                ->controller(AssetController::class)
                ->group(function () {
                    Route::get('/', 'index')->name('index');
                    Route::get('/{assetId}', 'show')->name('show');
                    Route::post('/', 'store')->name('store');
                    Route::put('/{assetId}', 'update')->name('update');
                    Route::delete('/{assetId}', 'delete')->name('delete');
                    Route::post('/{assetId}/restore', 'restore')->name('restore');
                });

            // Rules
            Route::prefix('rules')
                ->name('rule.')
                ->controller(RuleController::class)
                ->group(function () {
                    Route::post('/', 'store')->name('store');
                    Route::put('/{ruleId}', 'update')->name('update');
                    Route::delete('/{ruleId}', 'delete')->name('delete');
                    Route::post('/{ruleId}/reorder', 'reorder')->name('reorder');
                    Route::post('/reorder-all', 'reorderAll')->name('reorder_all');
                });
        });
});
