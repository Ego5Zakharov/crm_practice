<?php

namespace App\Http\Controllers\DesignPatterns\ObjectPool\Strategy;

use App\Http\Controllers\DesignPatterns\ObjectPool\FactoryMethod\PhoneFactory;

class StrategyController
{
    // когда у нас есть основной класс, который в зависимости от реализации
    // выдает результат
    public function handle(): void
    {
        $readHeadDuck = new RedHeadDuck(new FlyWithoutWings());
        $readHeadDuck->fly();

        $greenHeadDuck = new RedHeadDuck(new FlyWithWings());
        $greenHeadDuck->fly();
    }
}