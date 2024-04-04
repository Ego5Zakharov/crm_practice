<?php

namespace App\Kernel\Middlewares;

use App\Kernel\Auth\Auth;
use App\Models\Token;
use App\Models\User;

class ApiAuthMiddleware extends Middleware
{
    public function handle(): bool
    {
        $request = request();

        if ($request->server['HTTP_AUTHORIZATION']) {
            if (str_starts_with($request->server['HTTP_AUTHORIZATION'], 'Bearer')) {
                $bearerToken = explode(' ', $request->server['HTTP_AUTHORIZATION'])[1];

                $token = Token::query()->where('access_token', '=', $bearerToken)->first();

                if ($token) {
                    Auth::setUser($token->user());

                    return true;
                }
            }
        }

        return false;
    }
}