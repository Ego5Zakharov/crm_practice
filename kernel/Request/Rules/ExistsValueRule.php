<?php

namespace App\Kernel\Request\Rules;

class ExistsValueRule
{
    public static function handle(mixed $valueName): string
    {
        return "Такого аргумента - $valueName не существует.";
    }
}