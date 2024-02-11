<?php

namespace App\Kernel\Router;

use JetBrains\PhpStorm\NoReturn;

class Router
{
    private array $routes = [
        'GET' => [],
        'POST' => []
    ];

    public function __construct()
    {
        $this->initRoutes();
    }

    public function getRoutes()
    {
        return require_once APP_PATH . "/config/web.php";
    }

    public function initRoutes(): void
    {
        $routes = $this->getRoutes();

        foreach ($routes as $route) {
            $this->routes[$route->getMethod()][$route->getUri()] = $route->getAction();
        }
    }

    public function dispatch(string $uri, string $method): void
    {
        $route = $this->findRoute($uri, $method);
        if (is_array($route)) {
            $uri = $route[0];
            $action = $route[1];

            $class = new $uri();

            // Add middlewares

            call_user_func([$class, $action]);
        } else {
            call_user_func($route);
        }
    }

    public function findRoute(string $uri, string $method)
    {
        if (!isset($this->routes[$method][$uri])) {
            $this->notFound();
        }

        return $this->routes[$method][$uri];
    }


    #[NoReturn] public function notFound(): void
    {
        echo "Not Found|404";
        exit();
    }
}