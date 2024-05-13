<?php

namespace App\Kernel\Router;

use App\Http\Middlewares\MiddlewareKernel;
use App\Http\Requests\TestRequest;
use App\Kernel\Cache\NotFoundCacheSavePatchException;
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
     * @throws NotFoundCacheSavePatchException
     * Распаковывает роут
     */
    public function dispatch(string $uri, string $method): void
    {
        $route = $this->findRoute($uri, $method);

        if (is_array($route) && !$route[0] instanceof Closure) {
            $uri = $route[0][0];

            $action = $route[0][1];

            $middlewares = $route[1];

            /**
             * Применяем все общие middlewares на все роуты
             */
            $this->applyKernelMiddlewares();

            /**
             * Применяем middlewares которые есть на самом роуте
             */
            $this->applyMiddlewaresToRoute($middlewares);

            // dependency injection
            $class = new $uri(
                $this->request,
                $this->view,
                $this->session,
                $this->database
            );

            $this->injectRequest($class, $action);

            call_user_func([$class, $action], $this->request);
        } else {
            // если есть $middlewares у Closure - вызвать
            if (isset($route[1])) {
                $middlewares = $route[1];

                /**
                 * Применяем middlewares которые есть на самом роуте
                 */
                $this->applyMiddlewaresToRoute($middlewares);
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

    /**
     * @param string $classPath
     * @param string $actionPath
     * @return void
     * @throws ReflectionException
     * Подменяет $request на тот, что находится в методе контроллера
     * DI
     */
    public function injectRequest(mixed $classPath, string $actionPath): void
    {
        // подставлять Request в зависимости от нужных аргументов класса
        $reflectionClass = new ReflectionClass($classPath);

        if ($method = $reflectionClass->getMethod($actionPath)) {
            $parameters = $method->getParameters();
            foreach ($parameters as $parameter) {
                if ($parameter->getName() === "request") {
                    $request = $parameter->getType();

                    $requestPath = $request->getName();

                    $this->request = new $requestPath($_GET, $_POST, $_SERVER, $_COOKIE, $_FILES);
                }
            }
        }
    }

    /**
     * @return void
     * @throws NotFoundCacheSavePatchException
     * Применяет общие middlewares ко всем роутам
     */
    public function applyKernelMiddlewares(): void
    {
        /**
         * @param $middlewareKernel MiddlewareKernel
         * Применяем middlewares
         */
        $middlewareKernel = new MiddlewareKernel();

        foreach ($middlewareKernel->getMiddlewares() as $middlewarePath) {
            (new $middlewarePath())->handle();
        }
    }

    /**
     * @param array $middlewares
     * @return void
     * Применяет middlewares к определенному роуту
     */
    public function applyMiddlewaresToRoute(array $middlewares = []): void
    {
        foreach ($middlewares as $middleware) {
            $middleware = new $middleware();
            $middleware->handle();
        }
    }

    #[NoReturn] public function notFound(): void
    {
        http_response_code(404);
        echo "Not Found|404";
        exit();
    }
}