<?php

namespace App\Http\Controllers\DesignPatterns\ObjectPool\FactoryMethod;

class FactoryMethodController
{
    // когда у нас есть несколько объектов реализовывающих одинаковый интерфейс
    public function handle(): void
    {
        $factory = new PhoneFactory();

        dump($factory->createCellPhone());

        dump($factory->createSmartPhone());

    }
}