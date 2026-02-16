<?php

use App\Http\Controllers\AiController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProjectController;

Route::post('ping', [AuthController::class,'ping']);

Route::post('/register', [AuthController::class,'register']);
Route::post('/login', [AuthController::class,'login']);

Route::middleware(['auth:sanctum','throttle:60,1'])->group(function () {
    Route::post('logout', [AuthController::class,'logout']);
});

Route::post('/ai/chat', [AiController::class, 'chat']);
