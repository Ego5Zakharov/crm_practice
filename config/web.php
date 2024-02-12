<?php

use App\Kernel\Http\Controllers\TestController;
use App\Kernel\Route\Route;

return [
    Route::get('/test', [TestController::class, 'testGet']),
    Route::post('/post', [TestController::class, 'testPost']),

    Route::get('/', function () {
        dd('main');
    })
];
