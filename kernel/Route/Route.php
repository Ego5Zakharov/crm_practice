<?php

namespace App\Kernel\Route;

use App\Kernel\Request\Request;
use App\Kernel\Router\Router;
use Dotenv\Dotenv;
use InvalidArgumentException;

class Route
{

    private static string $prefix = "";
    private static array $routes = [];
    // данные этого роута
    private array $params = [];
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
        array  $middlewares = []
    ): Route
    {
        $route = new Route('GET', self::$prefix . $uri, $action, $middlewares);

        self::$routes[] = $route;

        return $route;
    }

    public static function post(
        string $uri,
        mixed  $action,
        array  $middlewares = []
    ): Route
    {
        $route = new Route('POST', self::$prefix . $uri, $action, $middlewares);

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
    public function getParams(): array
    {
        $uri = $this->getUri(); // Получаем URI из объекта

        // Используем регулярное выражение для поиска всех параметров в URI
        preg_match_all('/\{([^}]+)\}/', $uri, $matches);

        // В $matches[1] будут храниться найденные имена параметров
        $paramNames = $matches[1];

        // Текущий URI запроса
        $currentUri = \request()->uri();

        // URI маршрута
        $routeUri = $this->getUri();

        // Получаем части URI
        $currentUriParts = explode('/', trim($currentUri, '/'));
        $routeUriParts = explode('/', trim($routeUri, '/'));

        // Извлекаем значения параметров
        $params = [];
        foreach ($routeUriParts as $index => $part) {
            if (str_contains($part, '{') && str_contains($part, '}')) {
                $paramName = trim($part, '{}') ?? null;
                $params[$paramName] = $currentUriParts[$index] ?? null;
            }
        }

        // Возвращаем массив значений параметров
        return $params;
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