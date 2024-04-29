<?php

use App\Http\Controllers\API\AdminController;
use App\Http\Controllers\API\LoginController;
use App\Http\Controllers\API\RegisterController;
use App\Kernel\Middlewares\ApiAuthMiddleware;
use App\Kernel\Route\Route;
use App\Http\Controllers\TestController;

return [
    Route::prefix('/api', function () {

        Route::prefix('/v1', function () {
            Route::post('/register/', [RegisterController::class, 'register']);
            Route::post('/login/', [LoginController::class, 'login']);

            Route::get('/checkJWT/', [AdminController::class, 'index']);

            // TODO передавать через request какие либо данные связанные, которые можно будет обрабатывать в контроллере
            Route::prefix('/users', function () {
//                Route::get('/', [TestController::class, 'index']);
                Route::get('/', [TestController::class, 'show']);
                Route::post('/', [TestController::class, 'store']);
            });
        });

        Route::prefix('/test', function () {
            Route::get('/', [TestController::class, 'index']);
        });
    })
];


// TODO ТЗ задания
// есть вопросы и ответы
// у каждого вопроса может быть много ответов(айди, оценка)
// каждый вопрос может содержать подвопросы(детей)
// это значит что можно будет перемещаться по дереву через childrens() и parent()

