<?php

namespace App\Kernel\Csrf;

use Random\RandomException;

class Csrf
{
    /**
     * @return string
     * @throws RandomException
     */
    public static function generateCsrfToken(): string
    {
        return bin2hex(random_bytes(32));
    }

    /**
     * @return string
     * @throws RandomException
     */
    public static function getCsrfToken(): string
    {
        $token = self::generateCsrfToken();

        $_SESSION['csrf_token'] = $token;

        return $token;
    }

    /**
     * @param string $token
     * @return bool
     */
    public static function checkCsrfToken(string $token): bool
    {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
}