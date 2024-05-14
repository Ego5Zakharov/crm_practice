<?php

namespace App\Http\Middlewares;

use App\Kernel\Cache\NotFoundCacheSavePatchException;
use App\Kernel\Middlewares\Middleware;
use App\Kernel\Throttle\Throtlle;

class ThrotlleMiddleware extends Middleware
{
    /**
     * @return void
     * @throws NotFoundCacheSavePatchException
     */
    public function handle(): void
    {
        Throtlle::rateLimiter(150);
    }
}