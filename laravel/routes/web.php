<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\BranchController;
use App\Http\Controllers\Web\BusinessController;
use App\Http\Controllers\Web\ServiceController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
|
| Routes that do NOT require authentication
|
*/

Route::view('/', 'pages.welcome')->name('home');
Route::view('dev', 'pages.dev')->name('dev');
Route::view('myAppointments', 'pages.myAppointments')->name('myAppointments');

Route::controller(AuthController::class)->group(function () {
    // Show login/register forms
    Route::get('/login', 'showAuth')->name('login');
    Route::get('/register', 'showAuth')->name('register');

    // Handle login/register submission
    Route::post('/login', 'login')->name('login.submit');
    Route::post('/register', 'register')->name('register.submit');
});


/*
|--------------------------------------------------------------------------
| Protected Routes
|--------------------------------------------------------------------------
|
| Routes that require the user to be authenticated
|
*/
Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', fn() => view('pages.dashboard'))->name('dashboard');

    // Logout
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    /*
    |--------------------------------------------------------------------------
    | Test Admin Routes (Businesses, Branches, Services)
    |--------------------------------------------------------------------------
    */
    Route::prefix('test-admin')->name('test.')->middleware(['auth'])->group(function () {

        // Root of test-admin
        Route::get('/', [BusinessController::class, 'index'])->name('index');

        // Businesses
        Route::prefix('business')->name('business.')->controller(BusinessController::class)->group(function () {
            Route::post('/', 'store')->name('store'); // Create
            Route::put('/{businessId}', 'update')->name('update'); // Update
            Route::delete('/{businessId}', 'delete')->name('delete'); // Delete
            Route::post('/{businessId}/restore', 'restore')->name('restore'); // Restore soft-deleted
        });

        // Branches
        Route::prefix('branch')->name('branch.')->controller(BranchController::class)->group(function () {
            Route::post('/', 'store')->name('store'); // Create
            Route::put('/{branchId}', 'update')->name('update'); // Update
            Route::delete('/{branchId}', 'delete')->name('delete'); // Delete
        });

        // Services
        Route::prefix('service')->name('service.')->controller(ServiceController::class)->group(function () {
            Route::post('/', 'store')->name('store'); // Create
            Route::put('/{serviceId}', 'update')->name('update'); // Update
            Route::delete('/{serviceId}', 'delete')->name('delete'); // Delete
        });
    });
});
