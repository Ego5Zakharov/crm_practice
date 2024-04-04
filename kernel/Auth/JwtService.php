<?php

namespace App\Kernel\Auth;

use App\Kernel\Database\Model;
use Exception;

class JwtService
{
    private static ?string $secretKey = null;
    private static string $token;

    /**
     * @param array $payload - данные в токене
     * @param mixed $start_at - время начала действия токена
     * @param mixed $expires_st - время конца действия токена
     * @return string
     * @throws Exception
     */
    public static function createToken(Model $model, mixed $start_at, mixed $expires_st): string
    {
        if (!self::$secretKey) {
            throw new Exception("Secret key must be non-null");
        }

        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);

        $functionPayload['start_at'] = $start_at;
        $functionPayload['expires_at'] = $expires_st;
        $functionPayload['user_id'] = $model->getAttribute('id');

        $functionPayload = json_encode($functionPayload);

        $base64UrlHeader = self::strEncode($header);

        $base64Payload = self::strEncode($functionPayload);

        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64Payload, self::$secretKey, true);

        $base64Signature = self::strEncode($signature);

        self::$token = $base64UrlHeader . "." . $base64Payload . "." . $base64Signature;

        return self::$token;
    }

    /**
     * @param string $token
     * @return array
     * @throws Exception
     */
    public static function encodeToken(string $token): array
    {
        $parts = explode('.', $token);

        $header = self::strDecode($parts[0]);
        $payload = self::strDecode($parts[1]);
        $signature = self::strDecode($parts[2]);

        return [
            'header' => json_decode($header),
            'payload' => json_decode($payload),
            'signature' => json_decode($signature)
        ];
    }

    public static function generateSecretKey(): string
    {
        self::$secretKey = uniqid(mt_rand(10, 999), true);

        return self::$secretKey;
    }

    private static function strEncode(string $string): array|string
    {
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($string));
    }

    private static function strDecode(string $string): string
    {
        return base64_decode($string);
    }

    public static function getSecretKey(): string
    {
        return self::$secretKey;
    }

    public static function getToken(): ?string
    {
        return self::$token ?? null;
    }
}