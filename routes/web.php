<?php

use App\Http\Controllers\DesignPatterns\Memento\MementoController;
use App\Http\Controllers\DesignPatterns\ObjectPool\Builder\BuilderController;
use App\Http\Controllers\DesignPatterns\ObjectPool\FactoryMethod\FactoryMethodController;
use App\Http\Controllers\DesignPatterns\ObjectPool\ObjectPool\ObjectPoolController;
use App\Http\Controllers\DesignPatterns\ObjectPool\Strategy\StrategyController;
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


    Route::get('/', [TestController::class, 'testViesw']),

    Route::get('/create', [TestController::class, 'create']),
    Route::post('/post', [TestController::class, 'store']),


    Route::get('/factoryMethod', [FactoryMethodController::class, 'handle']),
    Route::get('/strategy', [StrategyController::class, 'handle']),
    Route::get('/builder', [BuilderController::class, 'handle']),
    Route::get('/objectPool', [ObjectPoolController::class, 'handle']),
    Route::get('/memento', [MementoController::class, 'handle']),
    Route::get('/prototype', [PrototypeController::Class, 'handle']),

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