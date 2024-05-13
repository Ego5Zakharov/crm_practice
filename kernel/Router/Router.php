<?php

namespace App\Kernel\Router;

use App\Http\Requests\TestRequest;
use App\Kernel\Database\Database;
use App\Kernel\Request\Request;
use App\Kernel\Route\Route;
use App\Kernel\Session\Session;
use App\Kernel\View\View;
use Closure;
use JetBrains\PhpStorm\NoReturn;
use ReflectionClass;
use ReflectionException;
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
                // Извлекаем URI маршрута без параметров
                $uriWithoutParams = $this->stripParamsFromUri($route->getUri());
                if (!str_ends_with($uriWithoutParams, '/')) {
                    $uriWithoutParams .= '/';
                }
                // Добавляем маршрут в массив маршрутов
                $this->routes[$route->getMethod()][$uriWithoutParams] = [
                    $route->getAction(),
                    $route->getMiddlewares(),
                    'params' => $route->getParams()
                ];
            }
        }
    }

    private function stripParamsFromUri(string $uri): string
    {
        // Если URI заканчивается на слеш, удаляем параметры, иначе возвращаем URI без изменений
        if (str_ends_with($uri, '/')) {
            $uriWithoutParams = preg_replace('/\/\{[^\/}]+\}|\/\{[^\/}]+\}$/u', '', $uri);
        } else {
            $uriWithoutParams = $uri;
        }

        // Добавляем слеш в конец URI
        return rtrim($uriWithoutParams, '/') . '/';
    }


    /**
     * @throws ReflectionException
     */
    public function dispatch(string $uri, string $method): void
    {
        $route = $this->findRoute($uri, $method);
        if (is_array($route) && !$route[0] instanceof Closure) {
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

            // подставлять Request в зависимости от нужных аргументов класса
            $reflectionClass = new ReflectionClass($class);

            if ($method = $reflectionClass->getMethod($action)) {
                $parameters = $method->getParameters();
                foreach ($parameters as $parameter) {
                    if ($parameter->getName() === "request") {
                        $request = $parameter->getType();

                        $requestPath = $request->getName();

                        $this->request = new $requestPath($_GET, $_POST, $_SERVER, $_COOKIE, $_FILES);
                    }
                }
            }

            call_user_func([$class, $action], $this->request);
        } else {
            // если есть $middlewares у Closure - вызвать
            if (isset($route[1])) {
                $middlewares = $route[1];

                // применяем middlewares
                foreach ($middlewares as $middleware) {
                    $middleware = new $middleware();
                    $middleware->handle();
                }
            }

            call_user_func($route[0], $this->request);
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
        http_response_code(404);
        echo "Not Found|404";
        exit();
    }
}