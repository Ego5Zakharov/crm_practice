<?php

namespace App\Kernel\Route;

class Route
{

    public function __construct(
        private string $method,
        private string $uri,
        private mixed  $action,
    )
    {

    }

    public static function get(
        string $uri,
        mixed  $action,
    ): Route
    {
        return new Route('GET', $uri, $action);
    }

    public static function post(
        string $uri,
        mixed  $action,
    ): Route
    {
        return new Route('POST', $uri, $action);
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