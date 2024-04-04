<?php

namespace App\Kernel\Router;

use App\Kernel\Database\Database;
use App\Kernel\Request\Request;
use App\Kernel\Route\Route;
use App\Kernel\Session\Session;
use App\Kernel\View\View;
use Closure;
use JetBrains\PhpStorm\NoReturn;
use Symfony\Component\VarDumper\Dumper\DataDumperInterface;

class Router
{
    private array $routes = [
        'GET' => [],
        'POST' => []
    ];

    public function __construct(
        public Request  $request,
        public View     $view,
        public Session  $session,
        public Database $database
    )
    {
        $this->initRoutes();
    }

    public function requireRoutes(): array
    {
        $routeFiles = scandir(base_path() . '/routes');
        $routes = [];
        foreach ($routeFiles as $index => $file) {
            if ($file !== "."
                && $file !== ".."
                && is_file(base_path() . "/routes/" . $file)
            ) {
                $routes[] = require_once base_path() . "/routes/" . $file;
            }
        }

        $additionallyRoutes = [];

        foreach ($routes as $index => $route) {
            if (is_array($route)) {
                foreach ($route as $iterator => $value) {
                    if ($value instanceof Route) {
                        $additionallyRoutes[] = $value;
                    }
                }
            }
        }

        $additionallyRoutes = [$additionallyRoutes];

        $routes[] = $additionallyRoutes;

        return call_user_func_array('array_merge', $routes);
    }

    public function getRoutes(): array
    {
        $routes = $this->requireRoutes();

        foreach ($routes as $key => $route) {

            if (!is_array($route)) {
                unset($routes[$key]);
                continue;
            }

            foreach ($route as $index => $value) {
                if (!$value && !$value instanceof Route) {
                    unset($route[$index]);
                }
            }

        }

        return $routes;
    }

    public function initRoutes(): void
    {
        $routeList = $this->getRoutes();

        foreach ($routeList as $routeListIndex => $routeValue) {
            foreach ($routeValue as $route) {
                $this->routes[$route->getMethod()][$route->getUri()] = [$route->getAction(), $route->getMiddlewares()];
//                if($route instanceof Closure){
//                    dd(123);
//                }
//                $this->routes[$route->getMethod()][$route->getUri()][2] = $route->getMiddlewares();
            }
        }
    }

    public function dispatch(string $uri, string $method): void
    {
        $route = $this->findRoute($uri, $method);

        if (is_array($route)) {
            $uri = $route[0][0];

            $action = $route[0][1];

            $middlewares = $route[1];

            // применяем middlewares
            foreach ($middlewares as $middleware) {
                $middleware = new $middleware();
                $middleware->handle();
            }

            // dependency injection
            $class = new $uri(
                $this->request,
                $this->view,
                $this->session,
                $this->database
            );

            // TODO
            // Add middlewares

            call_user_func([$class, $action]);
        } else {
            call_user_func($route);
        }
    }

    public function findRoute(string $uri, string $method)
    {
//        dump($uri,$method);
//        dd($this->routes);
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