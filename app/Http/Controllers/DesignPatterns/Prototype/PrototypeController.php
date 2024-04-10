<?php

namespace App\Http\Controllers\DesignPatterns\Prototype;

class PrototypeController
{

    public function handle(): void
    {
        $original = new Sheep('Jolly');
        echo $original->getName();


        $clone = clone $original;
        echo $clone->getName();
    }
}
