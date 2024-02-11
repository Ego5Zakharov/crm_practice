<?php

namespace App\Kernel;

use App\Kernel\Container\Container;

final class App
{
    public Container $container;

    public function __construct()
    {
        $this->container = new Container();
    }

    public function run(): void
    {
        $this->container->router->dispatch(
            $this->container->request->uri(),
            $this->container->request->method()
        );
    }
}