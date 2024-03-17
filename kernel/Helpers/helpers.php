<?php

// helpers

use App\Kernel\Collections\Collection;
use App\Kernel\Config\Config;
use App\Kernel\Json\Response;
use App\Kernel\Request\Request;
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

if (!function_exists('config')) {
    function config(string $key, ?string $default = null): mixed
    {
        return Config::get($key, $default);
    }
}
if (!function_exists('response')) {
    function response(
        array $data = [],
        int   $status = 200,
        array $headers = [],
        array $options = []
    ): Response
    {
        return new Response($data, $headers, $options, $status);
    }
}

if (!function_exists('request')) {
    function request(): Request
    {
        return Request::initialization();
    }
}