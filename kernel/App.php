<?php

namespace App\Kernel;

use App\Kernel\Container\Container;
use App\Kernel\Database\Database;

final class App
{
    public Container $container;

    public function __construct()
    {
        $this->container = new Container();
    }

    public function run()
    {
        // инициализация контейнера
    }
}