<?php

namespace App\Kernel\Request\Rules;

class MaxRule extends Rule
{
    /**
     * @param string $valueName
     * @param mixed $value - значение правила, которое должно быть больше или равно числа $requestValue
     * @param mixed $requestValue
     * @param array $arrayRules
     * @return true|array
     *
     * Возвращает результат и ошибку, если она есть
     * Если error возвращает false, значит ее нет
     */
    public static function handle(string $valueName, mixed $value, mixed $requestValue, array $arrayRules): true|array
    {
        $result = null;
        // проверяю в массиве, если есть int в правилах этой валидации - min отрабатывать от этого
        foreach ($arrayRules as $argumentName => $arrayRule) {
            // проверка по названию аргумента валидации
            if ($argumentName === $valueName) {
                foreach ($arrayRule as $rule) {
                    if ($rule === 'string') {
                        $result = $value >= strlen($requestValue);
                    } elseif ($rule === 'int') {
                        $result = intval($value) >= intval($requestValue);
                    } elseif ($rule === 'double') {
                        $result = doubleval($value) >= doubleval($requestValue);
                    } elseif ($rule === 'float') {
                        $result = floatval($value) >= floatval($requestValue);
                    }
                }
            }
        }

        // по умолчанию проверка проходит по длине строки
        if (
            !isset($arrayRules[$valueName]['double']) &&
            !isset($arrayRules[$valueName]) &&
            !isset($arrayRules[$valueName]['int']) &&
            !isset($arrayRules[$valueName]['float'])
        ) {
            $result = $value >= strlen($requestValue);
        }

        if ($result === false) {
            $error = MaxRule::getFeedback($valueName);
        }

        return [
            "result" => $result,
            "error" => $error ?? false
        ];
    }

    public static function getFeedback(string $valueName): string
    {
        return "Аргумент $valueName больше заданного количества";
    }
}