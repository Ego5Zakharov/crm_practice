<?php

namespace App\Http\Controllers\DesignPatterns\ObjectPool\Builder;

class BuilderController
{
    public function handle(): void
    {

        $burger = (new BurgerBuilder())
            ->setSize('large')
            ->addCheese()
            ->addPepperoni()
            ->addLettuce()
            ->addTomato()
            ->build();
        echo $burger->getBurgerInfo();
    }
}