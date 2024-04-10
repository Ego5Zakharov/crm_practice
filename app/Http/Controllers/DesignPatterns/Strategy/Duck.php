<?php

namespace App\Http\Controllers\DesignPatterns\ObjectPool\Strategy;

class Duck
{
    private FlyInterface $flyBehavior;

    public function __construct($flyBehavior)
    {
        $this->flyBehavior = $flyBehavior;
    }

    public function fly(): void
    {
        $this->flyBehavior->fly();
    }
}