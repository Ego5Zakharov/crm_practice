<?php

namespace App\Kernel\Collections;

use App\Kernel\Database\Model;
use App\Kernel\Database\Support\Arrayable;
use InvalidArgumentException;

class Collection implements Arrayable
{
    protected array $items = [];

    public function __construct(array $items = [])
    {
        $index = 0;

        foreach ($items as $key => $item) {
            if ($item instanceof Model) {
                $this->items[$key] = $item->getAttributes();

                if (!empty($item->getWithRelations())) {
                    $this->items[$key]['relations'] = $item->getWithRelations();
                }
            } else {
                $this->items[$index] = $item;
            }
            $index++;
        }


    }

    public function add(mixed $value): void
    {
        $this->items[] = $value;
    }

    public function map(callable $callable, Collection|array $data)
    {
        if ($data instanceof Collection) {
            $data = $data->toArray();
        }

        return array_map(function ($item) use ($callable) {
            return $callable($item);
        }, $data);
    }

    public function mapWithKeys(callable $callable, Collection|array $data)
    {
        if ($data instanceof Collection) {
            $data = $data->toArray();
        }
    }

    public function where(string $param, string $operator, mixed $value): Collection
    {
        $updatedArray = [];
        foreach ($this->items as $key => $item) {

            switch ($operator) {
                case '=':
                    if ($this->items[$key][$param] && $this->items[$key][$param] === $value) {
                        $updatedArray[] = $this->items[$key];
                    }
                    break;
                default:
                    throw new InvalidArgumentException("Unsupported operator: $operator");
            }

        }
        $this->items = $updatedArray;

        return $this;

    }

    public
    function toArray(): array
    {
        return $this->items;
    }

    public function setItems(array $items): void
    {
        $this->items = $items;
    }
}