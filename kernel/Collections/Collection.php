<?php

namespace App\Kernel\Collections;

use App\Kernel\Database\Model;
use App\Kernel\Database\Support\Arrayable;
use InvalidArgumentException;

class Collection implements Arrayable
{
    protected array $items = [];

    protected array $orWhereDataItems = [];
    protected array $operators = [
        'WHERE' => []
    ];

    protected int $whereCounter = 0;

    public function __get(string $key)
    {
        foreach ($this->items as $index => $value) {
            if (array_key_exists($key, $this->items)) {
                return $this->items[$key];
            } else if ($this->items['relations'][$key]) {
                return $this->items['relations'][$key];
            }
        }

        return null;
    }

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
                $this->items[$key] = $item;
            }
            $index++;
        }


    }

    public function add(mixed $value): void
    {
        $this->items[] = $value;
    }

    public function getWhereCounter(): int
    {
        return $this->whereCounter;
    }

    public function setWhereCounter(int $value): void
    {
        $this->whereCounter = $value;
    }

    public function incrementWhereCounter(): void
    {
        $this->whereCounter += 1;
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

    public function getOperators(): array
    {
        return $this->operators;
    }

    public function getOperator(string $key, string $operator)
    {
        return $this->operators[$operator][$key];
    }

    public function setOperator(mixed $value, string $operator): void
    {
        $this->operators[$operator] = $value;
    }

    public function mapWithKeys(callable $callable, Collection|array $data)
    {
        if ($data instanceof Collection) {
            $data = $data->toArray();
        }
    }

    // т.к идет постоянная перезапись текущих элементов в массиве, он отрабатывает условие
    // и отбирает нужные данные, теперь уже из отобранных данных проверяется новое условие
    // если у нас есть больше 1 where в запросе.
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
                case '!=':
                    if ($this->items[$key][$param] && $this->items[$key][$param] != $value) {
                        $updatedArray[] = $this->items[$key];
                    }
                    break;
                case '>':
                    if ($this->items[$key] && $this->items[$key][$param] > $value) {
                        $updatedArray[] = $this->items[$key];
                    }
                    break;
                case '<':
                    if ($this->items[$key] && $this->items[$key][$param] < $value) {
                        $updatedArray[] = $this->items[$key];
                    }
                    break;
                case '>=':
                    if ($this->items[$key] && $this->items[$key][$param] >= $value) {
                        $updatedArray[] = $this->items[$key];
                    }
                    break;
                case '<=':
                    if ($this->items[$key] && $this->items[$key][$param] <= $value) {
                        $updatedArray[] = $this->items[$key];
                    }
                    break;
                case 'LIKE':
                    if ($this->items[$key] && $this->items[$key][$param] && str_contains($this->items[$key][$param], $value)) {
                        $updatedArray[] = $this->items[$key];
                    }
                    break;
                case 'NOT LIKE':
                    if ($this->items[$key] && $this->items[$key][$param] && !str_contains($this->items[$key][$param], $value)) {
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

    public function orWhere(string $param, string $operator, mixed $value): Collection
    {
        $updatedArray = [];
        foreach ($this->items as $key => $item) {

            switch ($operator) {
                case '=':
                    if ($this->items[$key][$param] && $this->items[$key][$param] === $value) {
                        $updatedArray[] = $this->items[$key];
                    }
                    break;
                case '!=':
                    if ($this->items[$key][$param] && $this->items[$key][$param] != $value) {
                        $updatedArray[] = $this->items[$key];
                    }
                    break;
                case '>':
                    if ($this->items[$key] && $this->items[$key][$param] > $value) {
                        $updatedArray[] = $this->items[$key];
                    }
                    break;
                case '<':
                    if ($this->items[$key] && $this->items[$key][$param] < $value) {
                        $updatedArray[] = $this->items[$key];
                    }
                    break;
                case '>=':
                    if ($this->items[$key] && $this->items[$key][$param] >= $value) {
                        $updatedArray[] = $this->items[$key];
                    }
                    break;
                case '<=':
                    if ($this->items[$key] && $this->items[$key][$param] <= $value) {
                        $updatedArray[] = $this->items[$key];
                    }
                    break;
                case 'LIKE':
                    if ($this->items[$key] && $this->items[$key][$param] && str_contains($this->items[$key][$param], $value)) {
                        $updatedArray[] = $this->items[$key];
                    }
                    break;
                case 'NOT LIKE':
                    if ($this->items[$key] && $this->items[$key][$param] && !str_contains($this->items[$key][$param], $value)) {
                        $updatedArray[] = $this->items[$key];
                    }
                    break;


                default:
                    throw new InvalidArgumentException("Unsupported operator: $operator");
            }
        }

        $this->incrementWhereCounter();
        $this->orWhereDataItems[$this->getWhereCounter()] = $updatedArray;

        return $this;
    }


    public function toArray(): array
    {
        if ($this->getWhereCounter()) {
            $this->toArrayWithOperators();
        }

        return $this->items;
    }

    public function toArrayWithOperators(): void
    {
        $updatedArray = [];

        foreach ($this->orWhereDataItems as $index => $item) {
            foreach ($item as $value) {
                $updatedArray[] = $value;
            }
        }

        $this->setItems($updatedArray);
    }

    public function setItems(array $items): void
    {
        $this->items = $items;
    }
}