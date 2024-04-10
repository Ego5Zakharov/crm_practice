<?php

namespace App\Http\Controllers\DesignPatterns\ObjectPool\FactoryMethod;

class SmartPhoneInterface implements PhoneInterface
{
    public function call(): void
    {
        echo "Звонок с нормального телефона." . PHP_EOL;
    }
}