<?php

namespace App\Http\Middlewares;

class MiddlewareKernel
{
    /**
     * Middlewares которые применяются ко всем роутам в routes
     */
    protected array $middlewares = [
        ThrotlleMiddleware::class
    ];

    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }
}