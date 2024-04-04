<?php

namespace App\Kernel\Auth;

use App\Models\Token;
use App\Models\User;
use Exception;

trait HasApiTokens
{
    protected string $accessToken;

    protected ?User $user = null;

    /**
     * Возвращает true, если создаст токен для пользователя, иначе - false
     * @throws Exception
     */
    public function createToken(): ?bool
    {
        $user = debug_backtrace()[0]['object'];

        $this->user = $user;

        if (!$user instanceof User) {
            return null;
        }

        $this->accessToken = JwtService::createToken($user, date('y-m-d'), date('y-m-d'));

        $token = Token::query()->create([
            'name' => 'API',
            'access_token' => $this->accessToken,
            'signature' => JwtService::getSignature(),
            'user_id' => $user->getAttribute('id'),
        ]);

        return (bool)$token;
    }

    /**
     * Отдает последний токен пользователя
     */
    public function getToken()
    {
        return $this->user->tokens()[0]->last();
    }

    public function tokens(): ?array
    {
        return $this->hasMany(Token::class, 'user_id', 'id');
    }
}