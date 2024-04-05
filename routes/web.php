<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use App\Kernel\Auth\Auth;
use App\Kernel\Auth\JwtService;
use App\Kernel\Route\Route;
use App\Http\Controllers\TestController;

return [
    Route::get('/register', [RegisterController::class, 'registerView']),
    Route::post('/register', [RegisterController::class, 'register']),

    Route::get('/login', [LoginController::class, 'loginView']),
    Route::post('/login', [LoginController::class, 'login']),


    Route::get('/', [TestController::class, 'testView']),

    Route::get('/create', [TestController::class, 'create']),
    Route::post('/post', [TestController::class, 'store']),


//        $payload = [
//            'email' => 'egor_email',
//            'password' => '12345678'
//        ];
//
//        JwtService::generateSecretKey();
//        $token = JwtService::createToken($payload, time(), time() + (60 * 60));
//
//        dd(JwtService::encodeToken($token));

//        return view('users/dashboard');

];
//return [];