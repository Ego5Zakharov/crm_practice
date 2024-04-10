<?php

namespace App\Http\Controllers\DesignPatterns\ObjectPool\FactoryMethod;

class CellPhoneInterface implements PhoneInterface
{
    public function call(): void
    {
        echo "Звонок с клавишного телефона." . PHP_EOL;
    }
}