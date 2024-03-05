<?php

// helpers

use App\Kernel\Collections\Collection;
use Dotenv\Dotenv;

if (!function_exists('base_path')) {
    function base_path(): string
    {
        return APP_PATH;
    }
}
if (!function_exists('load_env')) {
    function load_env(): array
    {
        return Dotenv::createImmutable(base_path())->load();
    }
}
if (!function_exists('env')) {
    function env(string $argument, ?string $default = null): ?string
    {
        static $envValues;

        if (!$envValues) {
            $envValues = load_env();
        }

        return $envValues[$argument] ?? $default;
    }
}

if (!function_exists('app_url')) {
    function app_url(): ?string
    {
        return env('APP_URL');
    }
}

if (!function_exists('collect')) {
    function collect(array $array): Collection
    {
        return new Collection($array);
    }
}