<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use App\Kernel\Route\Route;
use App\Http\Controllers\TestController;

return [
    Route::prefix('/api', function () {

        Route::prefix('/v1', function () {
            Route::prefix('/users', function () {
                Route::get('/', [TestController::class, 'index']);
                Route::post('/', [TestController::class, 'store']);
            });
        });

        Route::prefix('/test', function () {
            Route::get('/', [TestController::class, 'index']);
        });

        Route::prefix('/auth', function () {
            Route::post('/register', [RegisterController::class, 'register']);
            Route::post('/register', [LoginController::class, 'login']);
        });
    })
];
