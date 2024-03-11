<?php

namespace App\Kernel\Route;

use App\Kernel\Router\Router;

class Route
{

    private static string $prefix = "";
    private static array $routes = [];
    private static Route $instance;

    public function __construct(
        private string $method,
        private string $uri,
        private mixed  $action,
        private array  $middlewares
    )
    {

    }

    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }

    public function getInstance(): Route
    {
        return self::$instance;
    }

    public static function prefix(string $name, callable $callable): array
    {
        $previousPrefix = self::$prefix;  // Сохранить текущий префикс

        self::$prefix .= $name;

        $callable();

        self::$prefix = $previousPrefix;  // Сбросить префикс к предыдущему состоянию

        return self::$routes;
    }


    public function setMiddlewares(array $middlewares): void
    {
        $this->middlewares = $middlewares;
    }

    public static function get(
        string $uri,
        mixed  $action,
    ): Route
    {
        $route = new Route('GET', self::$prefix . $uri, $action, []);
        self::$routes[] = $route;

        return $route;
    }

    public static function post(
        string $uri,
        mixed  $action,
    ): Route
    {
        $route = new Route('POST', self::$prefix . $uri, $action, []);
        self::$routes[] = $route;

        return $route;
    }

    public function setUri(string $uri): void
    {
        $this->uri = $uri;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function getAction(): mixed
    {
        return $this->action;
    }

    public function setAction(mixed $action): void
    {
        $this->action = $action;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function setMethod(string $method): void
    {
        $this->method = $method;
    }
}