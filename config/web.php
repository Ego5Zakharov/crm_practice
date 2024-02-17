<?php

use App\Kernel\Http\Controllers\TestController;
use App\Kernel\Route\Route;

return [
    Route::get('/get', [TestController::class, 'index']),
    Route::post('/post', [TestController::class, 'testPost']),

    Route::get('/', function () {
        dd('main');
    })
];
