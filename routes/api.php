<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{UserController, LoginController, BadgeController, AuthController};
use Illuminate\Auth\Events\Login;


route::post('/login', [LoginController::class, 'login']);

// rotte protette
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/auth/user', AuthController::class);
    Route::post('auth/logout', [LoginController::class, 'logout']);
    //Route::get('/user', AuthController::class);

    //USERS
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{user}', [UserController::class, 'show']);
    Route::put('/users/{user}', [UserController::class, 'update']);
    Route::delete('users/{user}', [UserController::class, 'delete']);
    Route::post('/users', [UserController::class, 'store']);
    Route::put('users/{user}/update-password', [UserController::class, 'updatePassword']);

    //BADGE
    Route::post('/check-in', [BadgeController::class, 'checkIn']);
    Route::post('/check-out', [BadgeController::class, 'checkOut']);
    Route::post('/create-note', [BadgeController::class, 'createNote']);
    Route::get('/get-time-logs', [BadgeController::class, 'getTimeLogs']);
    // Route::middleware('role:Admin')->get('/getAllPunches', [BadgeController::class, 'index']);
    Route::get('/getAllPunches', [BadgeController::class, 'index']);
  });
