<?php

use App\Http\Controllers\Api\PostController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::middleware([\Illuminate\Http\Middleware\HandleCors::class])
    ->prefix('blog')
    ->group(function() {
        Route::post('/login', [PostController::class, 'login']);
        Route::get('/', [PostController::class, 'index']);
        Route::get('{id}', [PostController::class, 'show']);
        Route::delete('{id}', [PostController::class, 'destroy']);
        Route::middleware(['auth:api'])->group(function () {
            Route::post('/add', [PostController::class, 'create']);
            Route::delete('{id}', [PostController::class, 'destroy']);
            Route::put('/update/{id}', [PostController::class, 'update']);
        });
    });
