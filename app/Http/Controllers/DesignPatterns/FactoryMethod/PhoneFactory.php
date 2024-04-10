<?php

namespace App\Http\Controllers\DesignPatterns\ObjectPool\FactoryMethod;

class PhoneFactory
{
    public function createCellPhone(): CellPhoneInterface
    {
        return new CellPhoneInterface();
    }

    public function createSmartPhone(): SmartPhoneInterface
    {
        return new SmartPhoneInterface();
    }
}