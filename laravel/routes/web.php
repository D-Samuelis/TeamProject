<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\{
    SearchController,
    NotificationController,
    ChatbotController,
    RoleAssignmentController
};
use App\Http\Controllers\Web\Auth\AuthController;
use App\Http\Controllers\Web\Business\ManageBusinessController;
use App\Http\Controllers\Web\Branch\ManageBranchController;
use App\Http\Controllers\Web\Service\ManageServiceController;
use App\Http\Controllers\Web\Asset\AssetController;
use App\Http\Controllers\Web\Rule\RuleController;
use App\Http\Controllers\Web\Appointment\AppointmentController;
use App\Http\Controllers\Web\Book\BookController;
use App\Http\Controllers\Web\ProfileController;

/*
|--------------------------------------------------------------------------
| Public / Customer
|--------------------------------------------------------------------------
*/

Route::view('/', 'web.customer.welcome')->name('home');

Route::prefix('search')->name('search.')->controller(SearchController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/businesses/{businessId}', 'showBusiness')->name('business.show');
    Route::get('/branches/{branchId}', 'showBranch')->name('branch.show');
    Route::get('/services/{serviceId}', 'showService')->name('service.show');
});

Route::controller(BookController::class)->prefix('book')->name('book.')->group(function () {
    Route::get('/business/{businessId}',                                    'business')->name('business');
    Route::get('/business/{businessId}/service/{serviceId}',                'service')->name('service');
    Route::get('/business/{businessId}/service/{serviceId}/asset/{assetId}', 'asset')->name('asset');
});

Route::prefix('appointments')->name('appointment.')->controller(AppointmentController::class)->group(function () {
    Route::get('/slots', 'slots')->name('slots');
});

/*
|--------------------------------------------------------------------------
| Guest (Auth)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->controller(AuthController::class)->group(function () {
    Route::get('/login', 'showAuth')->name('login');
    Route::get('/register', 'showAuth')->name('register');
    Route::post('/login', 'login')->name('login.submit');
    Route::post('/register', 'register')->name('register.submit');
});

/*
|--------------------------------------------------------------------------
| Authenticated
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/settings', [ProfileController::class, 'updateSettings'])->name('profile.settings');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', fn() => view('web.manage.dashboard'))->name('dashboard');
    Route::get('/my-appointments', [AppointmentController::class, 'index'])->name('myAppointments');

    Route::prefix('notifications')->name('notifications.')->controller(NotificationController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/mark-all-read', 'markAllRead')->name('markAllRead');
        Route::post('/{id}/dismiss', 'dismiss')->name('dismiss');
        Route::post('/{id}/read', 'markAsRead')->name('markRead');
    });

    Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointment.store');

    Route::get('/chatbot', [ChatbotController::class, 'index']);
    Route::post('/chatbot/token', [ChatbotController::class, 'issueToken']);

    /*
    |--------------------------------------------------------------------------
    | Management (web.manage)
    |--------------------------------------------------------------------------
    */
    Route::prefix('manage')->name('manage.')->group(function () {

        Route::prefix('businesses')->name('business.')->group(function () {
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
        Route::prefix('branches')->name('branch.')->controller(ManageBranchController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/{branchId}', 'show')->name('show');
            Route::post('/', 'store')->name('store');
            Route::put('/{branchId}', 'update')->name('update');
            Route::delete('/{branchId}', 'delete')->name('delete');
            Route::patch('/{branchId}/restore', 'restore')->name('restore');
        });

        // Services
        Route::prefix('services')->name('service.')->controller(ManageServiceController::class)->group(function () {
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
        Route::prefix('assets')->name('asset.')->controller(AssetController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/{assetId}', 'show')->name('show');
            Route::post('/', 'store')->name('store');
            Route::put('/{assetId}', 'update')->name('update');
            Route::delete('/{assetId}', 'delete')->name('delete');
            Route::post('/{assetId}/restore', 'restore')->name('restore');
        });

        // Rules
        Route::prefix('rules')->name('rule.')->controller(RuleController::class)->group(function () {
            Route::post('/', 'store')->name('store');
            Route::put('/{ruleId}', 'update')->name('update');
            Route::delete('/{ruleId}', 'delete')->name('delete');
            Route::post('/{ruleId}/reorder', 'reorder')->name('reorder');
            Route::post('/reorder-all', 'reorderAll')->name('reorder_all');
        });

        // Manage Appointments
        Route::prefix('appointments')->name('appointment.')->controller(AppointmentController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/{appointmentId}', 'show')->name('show');
            Route::delete('/{appointmentId}', 'delete')->name('delete');
        });
    });
});
