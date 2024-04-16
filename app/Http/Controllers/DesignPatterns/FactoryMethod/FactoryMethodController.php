<?php

namespace App\Http\Controllers\DesignPatterns\FactoryMethod;

use App\Http\Controllers\DesignPatterns\ObjectPool\FactoryMethod\PhoneFactory;
use App\Models\User;

class FactoryMethodController
{
    // когда у нас есть несколько объектов реализовывающих одинаковый интерфейс
    public function handle(): void
    {
        $factory = new PhoneFactory();
        User::query()->where('id', '=', '5')->whereHas('users', function ($query) {
            $query->where('id', '=', 2);
        });
        dump($factory->createCellPhone());

        dump($factory->createSmartPhone());

    }
}