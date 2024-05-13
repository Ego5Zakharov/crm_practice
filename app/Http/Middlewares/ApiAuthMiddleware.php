<?php

namespace App\Http\Middlewares;

use App\Kernel\Auth\Auth;
use App\Kernel\Middlewares\Middleware;
use App\Models\Token;

class ApiAuthMiddleware extends Middleware
{
    /**
     * @return true|void
     */
    public function handle()
    {
        $request = request();

        if ($request->server['HTTP_AUTHORIZATION']) {
            if (str_starts_with($request->server['HTTP_AUTHORIZATION'], 'Bearer')) {
                $bearerToken = explode(' ', $request->server['HTTP_AUTHORIZATION'])[1];

                $token = Token::query()->where('access_token', '=', $bearerToken)->first();

                if ($token && $token->getAttribute('expires_at') <= time()) {
                    $this->httpError(401, ['message' => 'Token is expire']);
                }

                if ($token) {
                    Auth::setUser($token->user());
                    return true;
                }
            }
        }
        $this->httpError(401, ['message' => 'Unauthorized']);
    }
}