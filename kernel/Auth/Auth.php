<?php

namespace App\Kernel\Auth;

use AllowDynamicProperties;
use App\Kernel\Session\Session;
use App\Models\User;

class Auth
{
    protected static ?Auth $instance = null;
    protected static Session $session;

    protected mixed $user;
    private array $algorithms = [
        'BCRYPT' => '2y',
        'DEFAULT' => "2y",
        'ARGON2I' => 'argon2i',
        'ARGON2ID' => 'argon2id'
    ];

    public function __construct(Session $session)
    {
        self::$session = $session;
    }

    public static function initialization(Session $session): Auth
    {
        $auth = new Auth($session);

        self::$instance = $auth;

        return $auth;
    }

    // по умолчанию алгоритм шифрования берется из конфига config.hash
    // если значение $hashAlgorithm !== null - используем алгоритм шифрования выбранный пользователем
    public static function hashPassword($password, ?string $hashAlgorithm = null): string
    {
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

    // проверяет, есть ли такой емейл и если есть, проверяет пароли
    public static function attempt(string $email, string $password): bool
    {
        $user = User::query()->where('email', '=', $email)->first();

        if (!$user) {
            return false;
        }

        $isVerified = self::verifyPassword($password, $user->getAttribute('password'));

        if ($isVerified) {
            self::$instance::$session->set('is_auth', true);
            self::$instance->user = $user;
            return true;
        }

        return false;
    }

    public static function attemptAPI(string $email, string $password): false|User|array
    {
        $user = User::query()->where('email', '=', $email)->first();

        if (!$user) {
            return false;
        }

        $isVerified = self::verifyPassword($password, $user->getAttribute('password'));

        if ($isVerified) {
            self::$instance->user = $user;
        }

        return ($isVerified) ? $user : false;
    }

    // проверяет авторизован ли пользователь в сессии
    public static function isAuth(): null|bool|string
    {
        return self::$session->get('is_auth');
    }

    // выход из сессии
    public static function logout(): void
    {
        self::$instance::$session->unset('is_auth');
    }

    public static function user()
    {
        return self::$instance->user;
    }

    public static function setUser(User $user): void
    {
        self::$instance->user = $user;
    }
}