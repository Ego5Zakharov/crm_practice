<?php

namespace App\Kernel\Http\Middlewares;

use App\Kernel\Request\Request;

abstract class Middleware
{
    public abstract function handle(Request $request): mixed;
}