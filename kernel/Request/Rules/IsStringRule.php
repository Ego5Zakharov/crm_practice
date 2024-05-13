<?php

namespace App\Kernel\Request\Rules;

class IsStringRule extends Rule
{
    /**
     * @param string $valueName
     * @param mixed $value
     * @return true|array
     *
     * Возвращает результат и ошибку, если она есть
     * Если error возвращает false, значит ее нет
     */
    public static function handle(string $valueName, mixed $value): true|array
    {
        $result = is_string((int)$value);

        if ($result === false) {
            $error = IsStringRule::getFeedback($valueName);
        }

        return [
            "result" => $result,
            "error" => $error ?? false
        ];
    }

    public static function getFeedback(string $valueName): string
    {
        return "Аргумент $valueName не является строкой.";
    }
}