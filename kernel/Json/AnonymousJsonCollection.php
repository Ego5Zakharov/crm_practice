<?php

namespace App\Kernel\Json;

class AnonymousJsonCollection
{
    public array $items = [];

    public function __construct(array $items)
    {
        $this->items = $items;
    }
}