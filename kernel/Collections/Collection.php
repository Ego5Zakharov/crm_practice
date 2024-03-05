<?php

namespace App\Kernel\Collections;

use App\Kernel\Database\Support\Arrayable;

class Collection
{
    protected array $items = [];

    public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    public function add(mixed $value): void
    {
        $this->items[] = $value;
    }

    public function all(): array
    {
        return $this->items;
    }
}