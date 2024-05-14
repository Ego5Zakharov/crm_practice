<?php

namespace App\Http\Middlewares;

use App\Kernel\Csrf\Csrf;

class MiddlewareKernel
{
    /**
     * Middlewares которые применяются ко всем роутам в routes
     */
    protected array $middlewares = [
        ThrotlleMiddleware::class,
        CsrfMiddleware::class
    ];

    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }
}