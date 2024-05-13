<?php

namespace App\Kernel\Middlewares;

use App\Kernel\Auth\Auth;
use App\Kernel\Cache\NotFoundCacheSavePatchException;
use App\Kernel\Throttle\Throtlle;
use App\Models\Token;
use App\Models\User;

class ThrotlleMiddleware extends Middleware
{
    /**
     * @return void
     * @throws NotFoundCacheSavePatchException
     */
    public function handle(): void
    {
        Throtlle::rateLimiter(15);
    }
}