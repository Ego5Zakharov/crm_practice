<?php

namespace App\Kernel\Session;

class Session
{
    public function __construct()
    {
        $this->init();
    }

    public function init(): void
    {
        session_start();
    }

    // заканчивает сессию и уничтожает все данные
    public function destroy(): void
    {
        session_destroy();
    }

    // заканчивает сессию и уничтожает все данные
    public function abort(): void
    {
        session_abort();
    }

    public function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public function unset(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public function get(string $key, ?string $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }
}