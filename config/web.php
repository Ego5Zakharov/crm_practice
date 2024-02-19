<?php

use App\Kernel\Http\Controllers\TestController;
use App\Kernel\Route\Route;

return [
    Route::get('/', [TestController::class, 'index']),
    Route::get('/create', [TestController::class, 'create']),
    Route::post('/post', [TestController::class, 'store']),

//    Route::get('/', function () {
//        return '/';
//    })

];
