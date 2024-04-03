<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use App\Kernel\Auth\Auth;
use App\Kernel\Route\Route;
use App\Http\Controllers\TestController;

return [
    Route::get('/register', [RegisterController::class, 'registerView']),
    Route::post('/register', [RegisterController::class, 'register']),

    Route::get('/login', [LoginController::class, 'loginView']),
    Route::post('/login', [LoginController::class, 'login']),


    Route::get('/', [TestController::class, 'index']),

    Route::get('/create', [TestController::class, 'create']),
    Route::post('/post', [TestController::class, 'store']),
//

    Route::get('/', function () {
        return view('users/dashboard');
    })


];
//return [];