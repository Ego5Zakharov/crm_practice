<?php

namespace App\Kernel\Database;

abstract class Entity
{
    protected array $data = [];

    public function __get(string $value)
    {
        if (array_key_exists($value, $this->data)) {
            return $this->data[$value];
        }
        return null;
    }

    public function __set(string $key, string $value)
    {
        $this->data[$key] = $value;
    }

    public function toArray(): array
    {
        return $this->data;
    }
}