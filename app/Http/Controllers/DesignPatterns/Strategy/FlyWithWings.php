<?php

namespace App\Http\Controllers\DesignPatterns\ObjectPool\Strategy;

class FlyWithWings implements FlyInterface
{
    public function fly(): void
    {
        echo "Полет!";
    }
}