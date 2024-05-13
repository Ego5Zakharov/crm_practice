<?php

namespace App\Kernel\Request;

use App\Kernel\Config\Config;
use App\Kernel\Request\Rules\ExistsValueRule;
use App\Kernel\Request\Rules\IsStringRule;

class Request
{
    public function __construct(
        public array $get,
        public array $post,
        public array $server,
        public array $cookies,
        public array $files,
        public array $errors = []
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

    public function query(): ?string
    {
        $url = config('app.url') . $this->server['REQUEST_URI'];

        return parse_url($url)['query'] ?? null;
    }

    public function params(): ?array
    {
        $url = $this->query();

        $parsedUrl = $url ? parse_url($url)['path'] : null;

        $queryParams = [];

        if ($parsedUrl) {
            parse_str($parsedUrl, $queryParams);
            return $queryParams;
        }

        return null;
    }


    public function fullUrl(): string
    {
        return config('app.url') . $this->server['REQUEST_URI'];
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

    public function input(string $argument, ?string $default = null)
    {
        return $this->get[$argument]
            ?? $this->post[$argument]
            ?? $this->params()[$argument]
            ?? $default;
    }

    public function has(string $argument, ?string $default = null): bool
    {
        $hasValue = $this->get[$argument]
            ?? $this->post[$argument]
            ?? $this->params()[$argument]
            ?? $default;

        return boolval($hasValue) ?? $default;
    }

    public function validate(array $arrayRules)
    {
        foreach ($arrayRules as $ruleName => $rules) {
            if ($this->has($ruleName)) {
                $ruleValue = $this->input($ruleName);

                foreach ($rules as $rule) {
                    $this->validateRule($ruleName, $rule, $ruleValue);
                }

            } else {
                $this->setError(
                    $ruleName, ExistsValueRule::handle($ruleName)
                );
            }
        }

        dd($this->getErrors());
    }

    public function validateRule(string $ruleName, string $rule, mixed $value): void
    {
        switch ($rule) {
            case "email":

                break;
            case "string":
                // если $result['error'] возвращает false - записываем в ошибку
                $result = IsStringRule::handle($ruleName, $value);

                if ($result && isset($result['error']) && $result['error'] !== false) {
                    $this->setError($ruleName, $result['error']);
                }

//                dd($this->getErrors());
                break;

            case "min":

                break;

            case "max":

                break;
            default:
                break;
        }
    }

    public function setError(string $ruleName, string $error): void
    {
        // Если в массиве уже есть элемент с таким именем правила, добавляем ошибку к существующему массиву
        if (isset($this->errors[$ruleName])) {
            $this->errors[$ruleName][] = $error;
        } else {
            // Если элемента с таким именем правила еще нет в массиве, создаем новый массив с ошибкой
            $this->errors[$ruleName] = [$error];
        }
    }


    public function getErrors(): array
    {
        return $this->errors;
    }

}