<?php

namespace App\Kernel\Database;

abstract class Model
{
    protected array $data = [];

    protected array $fillable = [];

    protected string $table;


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