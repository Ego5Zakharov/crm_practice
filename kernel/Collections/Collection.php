<?php

namespace App\Kernel\Collections;

use App\Kernel\Database\Support\Arrayable;

class Collection implements Arrayable
{
    protected array $items = [];

    public function __construct(array $items = [])
    {
        foreach ($items as $key => $item) {
            $this->items[$key] = $item->getAttributes();
            if (!empty($item->getWithRelations())) {
                $this->items[$key]['relations'] = $item->getWithRelations();
            }
        }

    }

    public function add(mixed $value): void
    {
        $this->items[] = $value;
    }

    public function toArray(): array
    {
        return $this->items;
    }
}