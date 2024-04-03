<?php

namespace App\Kernel\Auth;

class Auth
{
    private static ?Auth $instance = null;
    private array $algorithms = [
        'BCRYPT' => '2y',
        'DEFAULT' => "2y",
        'ARGON2I' => 'argon2i',
        'ARGON2ID' => 'argon2id'
    ];

    private function __construct()
    {
    }

    // по умолчанию алгоритм шифрования берется из конфига config.hash
    // если значение $hashAlgorithm !== null - используем алгоритм шифрования выбранный пользователем
    public static function hashPassword($password, ?string $hashAlgorithm = null): string
    {
        if (self::$instance === null) {
            self::$instance = new Auth();
        }

        if ($hashAlgorithm === null) {
            $algorithm = self::$instance->passwordAlgorithmTranslator(config('hash.hash_algorithm'));
            return password_hash($password, $algorithm);
        }

        return password_hash($password, $hashAlgorithm);
    }

    private function passwordAlgorithmTranslator(string $key)
    {
        return $this->algorithms[$key];
    }

    public static function verifyPassword(string $enteredPassword, string $storedHashedPassword): bool
    {
        return password_verify($enteredPassword, $storedHashedPassword);
    }
}