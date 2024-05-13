<?php

namespace App\Kernel\Request\Rules;

class ExistsValueRule
{
    public static function message(mixed $valueName): string
    {
        return "Такого аргумента - $valueName не существует.";
    }
}