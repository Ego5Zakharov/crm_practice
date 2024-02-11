<?php

namespace App\Kernel\Request;

class Request
{
    public function __construct(
        public readonly array $get,
        public readonly array $post,
        public readonly array $server,
        public readonly array $cookies,
        public readonly array $files,
    )
    {

    }

    public static function initialization(): Request
    {
        return new Request($_GET, $_POST, $_SERVER, $_COOKIE, $_FILES);
    }

    public function method()
    {
        return $this->server['REQUEST_METHOD'];
    }

    public function uri(): bool|string
    {
        return strtok($this->server['REQUEST_URI'], '?');
    }

    public function get(): array
    {
        return $this->get;
    }

    public function post(): array
    {
        return $this->post;
    }

    public function server(): array
    {
        return $this->server;
    }

    public function cookies(): array
    {
        return $this->cookies;
    }

    public function files(): array
    {
        return $this->files;
    }

}