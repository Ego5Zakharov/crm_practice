<?php

namespace App\Kernel\Auth;

use App\Models\User;
use Exception;

trait HasApiTokens
{
    protected string $accessToken;

    /**
     * @throws Exception
     */
    public function createToken()
    {
        $user = debug_backtrace()[0]['object'];

        if (!$user instanceof User) {
            return null;
        }

        $this->accessToken = JwtService::createToken($user, date('y-m-d'), date('y-m-d'));
    }

    /**
     * Отдает последний токен пользователя
     */
    public function getToken()
    {

    }

    public function tokens(): ?array
    {
        return $this->hasMany(Token::class, 'user_id', 'id');
    }
}