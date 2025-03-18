<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PostController;
use Illuminate\Http\Request;

Route::middleware([\Illuminate\Http\Middleware\HandleCors::class])
    ->prefix('blog')
    ->group(function() {
        Route::post('/add', [PostController::class, 'create']);
        Route::get('/', [PostController::class, 'index']);
        Route::get('{id}', [PostController::class, 'show']);
        Route::put('{id}', [PostController::class, 'update']);
        Route::delete('{id}', [PostController::class, 'destroy']);
    });

