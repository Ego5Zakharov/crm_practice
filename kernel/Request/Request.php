<?php

namespace App\Kernel\Request;

use App\Kernel\Request\Rules\ExistsValueRule;
use App\Kernel\Request\Rules\IsStringRule;
use App\Kernel\Request\Rules\MailRule;
use App\Kernel\Request\Rules\MaxRule;
use App\Kernel\Request\Rules\MinRule;

class Request
{
    /**
     * @param array $get
     * @param array $post
     * @param array $server
     * @param array $cookies
     * @param array $files
     * @param array $errors
     * @param array $arrayRules
     */
    public function __construct(
        public array $get,
        public array $post,
        public array $server,
        public array $cookies,
        public array $files,
        public array $errors = [],
        public array $arrayRules = [] // массив правил
    )
    {

    }

    /**
     * @return Request
     */
    public static function initialization(): Request
    {
        return new Request($_GET, $_POST, $_SERVER, $_COOKIE, $_FILES);
    }

    /**
     * @return mixed
     */
    public function method()
    {
        return $this->server['REQUEST_METHOD'];
    }

    /**
     * @return bool|string
     */
    public function uri(): bool|string
    {
        return strtok($this->server['REQUEST_URI'], '?');
    }

    /**
     * @return string|null
     */
    public function query(): ?string
    {
        $url = config('app.url') . $this->server['REQUEST_URI'];

        return parse_url($url)['query'] ?? null;
    }

    /**
     * @return array|null
     */
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


    /**
     * @return string
     */
    public function fullUrl(): string
    {
        return config('app.url') . $this->server['REQUEST_URI'];
    }

    /**
     * @return array
     */
    public function get(): array
    {
        return $this->get;
    }

    /**
     * @return array
     */
    public function post(): array
    {
        return $this->post;
    }

    /**
     * @return array
     */
    public function server(): array
    {
        return $this->server;
    }

    /**
     * @return array
     */
    public function cookies(): array
    {
        return $this->cookies;
    }

    /**
     * @return array
     */
    public function files(): array
    {
        return $this->files;
    }

    /**
     * @param string $argument
     * @param string|null $default
     * @return mixed|string|null
     */
    public function input(string $argument, ?string $default = null)
    {
        return $this->get[$argument]
            ?? $this->post[$argument]
            ?? $this->params()[$argument]
            ?? $default;
    }

    /**
     * @param string $argument
     * @param string|null $default
     * @return bool
     */
    public function has(string $argument, ?string $default = null): bool
    {
        $hasValue = $this->get[$argument]
            ?? $this->post[$argument]
            ?? $this->params()[$argument]
            ?? $default;

        return boolval($hasValue) ?? $default;
    }

    /**
     * @param array $arrayRules
     *
     * Метод валидирующий поля
     */
    public function validate(array $arrayRules): array
    {
        /**
         * @param array $arrayRules - массив правил валидации
         */
        $this->arrayRules = $arrayRules;

        $rulesArguments = [];

        foreach ($arrayRules as $ruleName => $rules) {
            /**
             * @param array $rules - правила элемента массива
             * @param string $ruleName - название переменной: apple, banana, juice
             * @param mixed $ruleValue - значение переменной
             *
             * @param mixed $rule - может содержать данные по типу
             * @param $rule ['value']
             * @param $rule ['name']
             */
            if ($this->has($ruleName)) {
                $ruleValue = $this->input($ruleName);

                foreach ($rules as $rule) {
                    /**
                     * Если имеются правила по типу min:3,max:5
                     * где min - название правила валидации, а 5 - значение
                     */
                    if (str_contains($rule, ':')) {
                        $ruleData = explode(":", $rule);

                        $ruleData = [
                            'name' => $ruleData[0],
                            'value' => $ruleData[1]
                        ];

                        // Добавляем новый элемент в $rulesArguments
                        $rulesArguments[$ruleName] = $ruleValue;

                        $this->validateRule($ruleName, $ruleData['name'], $ruleData['value'], $ruleValue);
                    } else {
                        /*
                         * Если нет правил похожих на структуру: min:3, max:5
                         */
                        // Добавляем новый элемент в $rulesArguments
                        $rulesArguments[$ruleName] = $ruleValue;

                        $this->validateRule($ruleName, $rule, $ruleValue, null);
                    }
                }

            } else {
                $this->setError(
                    $ruleName, ExistsValueRule::message($ruleName)
                );
            }
        }

        if (count($this->getErrors()) > 0) {
            return $this->getErrors();
        }

        return $rulesArguments;
    }


    /**
     * @param string $ruleName
     * @param string $rule
     * @param mixed $value
     * @param mixed $requestValue
     * @return void
     */
    public function validateRule(string $ruleName, string $rule, mixed $value, mixed $requestValue): void
    {
        switch ($rule) {
            case "email":
                $result = MailRule::handle($ruleName, $value);

                if ($result && isset($result['error']) && $result['error'] !== false) {
                    $this->setError($ruleName, $result['error']);
                }
                break;
            case "string":
                /*
                 * Если $result['error'] возвращает false - записываем в ошибку
                 */
                $result = IsStringRule::handle($ruleName, $value);

                if ($result && isset($result['error']) && $result['error'] !== false) {
                    $this->setError($ruleName, $result['error']);
                }
                break;
            case "min":
                $result = MinRule::handle($ruleName, $value, $requestValue, $this->arrayRules);

                if ($result && isset($result['error']) && $result['error'] !== false) {
                    $this->setError($ruleName, $result['error']);
                }
                break;
            case "max":
                $result = MaxRule::handle($ruleName, $value, $requestValue, $this->arrayRules);

                if ($result && isset($result['error']) && $result['error'] !== false) {
                    $this->setError($ruleName, $result['error']);
                }
                break;
            default:
                break;
        }
    }

    /**
     * @param string $ruleName
     * @param string $error
     * @return void
     */
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


    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Возвращает айди сессии текущего пользователя,
     * по этому айди можно отслеживать конкретного пользователя и его действия на сайте
     * @return ?string
     */
    public function getServerUserSession(): ?string
    {
        if (isset($_SERVER['HTTP_COOKIE'])) {
            $cookies = explode(';', $_SERVER['HTTP_COOKIE']);

            foreach ($cookies as $cookie) {
                $parts = explode('=', $cookie);

                $name = trim($parts[0]);
                if ($name === 'PHPSESSID') {
                    return trim($parts[1]);
                }
            }
        }
        return null;
    }

    /**
     * @return mixed
     */
    public function getIp()
    {
        return $this->server['REMOTE_ADDR'];
    }
}