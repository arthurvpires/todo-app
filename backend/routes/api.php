<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::controller(TaskController::class)->group(function () {
        Route::get('/tasks', 'index');
        Route::post('/tasks', 'store');
        Route::get('/tasks/{task}', 'show');
        Route::put('/tasks/{task}', 'update');
        Route::delete('/tasks/{task}', 'destroy');
    });

    Route::controller(UserController::class)->group(function () {
        Route::get('/users', 'index');
        Route::post('/users/create', 'store');
        Route::delete('/users/{user}', 'destroy');
    });

});
