<?php

namespace App\Kernel\Request\Rules;

class MailRule extends Rule
{
    /**
     * @param string $valueName
     * @param mixed $value
     * @param mixed $requestValue
     * @return true|array
     *
     * Возвращает результат и ошибку, если она есть
     * Если error возвращает false, значит ее нет
     */
    public static function handle(string $valueName, mixed $value): true|array
    {
        $result = filter_var($value,FILTER_VALIDATE_EMAIL);

        if ($result === false) {
            $error = MailRule::getFeedback($valueName);
        }

        return [
            "result" => $result,
            "error" => $error ?? false
        ];
    }

    public static function getFeedback(string $valueName): string
    {
        return "Аргумент $valueName не является валидным адресом почты.";
    }
}