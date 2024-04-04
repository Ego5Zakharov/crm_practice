<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\API\RegisterController;
use App\Kernel\Route\Route;
use App\Http\Controllers\TestController;

// TODO добавить уникальное хранение сигнатуры токена для каждого пользователя и хранить секретный ключ приложения для доступа к JWT в файле
// добавить

return [
    Route::prefix('/api', function () {

        Route::prefix('/v1', function () {
            Route::post('/register/', [RegisterController::class, 'register']);

            Route::prefix('/users', function () {
                Route::get('/', [TestController::class, 'index']);
                Route::post('/', [TestController::class, 'store']);
            });
        });

        Route::prefix('/test', function () {
            Route::get('/', [TestController::class, 'index']);
        });

    })
];
