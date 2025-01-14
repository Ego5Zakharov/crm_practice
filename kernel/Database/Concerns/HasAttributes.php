<?php

namespace App\Kernel\Database\Concerns;

use Exception;

trait HasAttributes
{
    // текущие данные для записи в базу данных
    protected array $attributes = [];

    // оригинальные данные записи подлежащие заполнению
    protected array $original = [];

    // измененные параметры
    protected array $changes = [];

    // при сохранении в бд какой тип данных будет использоваться
    protected array $casts = [];

    // не относящиеся к бд параметры
    protected array $appends = [];

    public function setAttribute(string $key, mixed $value): void
    {
        $this->attributes[$key] = $value;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function setAppends(array $appends): void
    {
        $this->appends = $appends;
    }

    public function setAttributes(array $attributes): void
    {
        $this->attributes = $attributes;
    }

    public function setCasts(array $casts): void
    {
        $this->casts = $casts;
    }

    public function getCasts(): array
    {
        return $this->casts;
    }

    public function setChanges(array $changes): void
    {
        $this->changes = $changes;
    }

    public function setOriginal(string $key, mixed $value): void
    {
        $this->original[$key] = $value;
    }

    public function setOriginals(array $values): void
    {
        $this->original = $values;
    }

    public function getAttribute(string $key): mixed
    {
        return $this->attributes[$key];
    }

    public function getOriginal(string $key)
    {
        return $this->original[$key];
    }

    public function getOriginals(): array
    {
        return $this->original;
    }

    public function getChanges(): array
    {
        return $this->changes;
    }


    /**
     * @param string $dataType
     * @param mixed $value
     * @return string|int|bool|float
     *
     * Применяет тип данных к переменной
     * @throws Exception
     */
    private function getCastType(string $dataType, mixed $value): string|int|bool|float
    {
        return match ($dataType) {
            "integer", "int" => (int)$value,
            "float", "double" => (float)$value,
            "boolean", "bool" => (bool)$value,
            "string", "str" => (string)$value,
            default => throw new Exception("Incorrect data type - $dataType"),
        };
    }
}