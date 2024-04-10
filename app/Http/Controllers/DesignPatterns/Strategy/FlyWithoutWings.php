<?php

namespace App\Http\Controllers\DesignPatterns\ObjectPool\Strategy;

class FlyWithoutWings implements FlyInterface
{
    public function fly(): void
    {
        echo "А я не могу летать :(" . "</br>";
    }
}