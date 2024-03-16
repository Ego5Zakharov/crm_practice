<?php

namespace App\Kernel\Database\Query;

use PDOStatement;

trait Queries
{
    protected string $query = "";

    protected PDOStatement $statement;

    // какое количество записей выбирать из sql запроса
    protected ?int $limitCount = null;

    protected int $whereCallsCount = 0;

    protected array $bindParams = [];

    protected string $uniqueHash = "";

    protected string $separator = "%%%";

    protected array $whereOperators = [
        '=',
        '<',
        '>',
        '>=',
        '<=',
        '!=',
        'LIKE',
        'NOT LIKE'
    ];

    public function getQuery(): string
    {
        return $this->query;
    }

    public function setQuery(string $query): void
    {
        $this->query = $query;
    }

    // TODO добавить позицию
    public function concatQuery(string $text): void
    {
        $this->query .= $text;
    }

    public function bindParam(mixed $key, mixed $value): void
    {
        $this->bindParams[$key] = $value;
    }

    public function getBindParam($key)
    {
        return $this->bindParams[$key];
    }

    public function getBindParams(): array
    {
        return $this->bindParams;
    }

    public function setBindParams(array $params): void
    {
        $this->bindParams = $params;
    }

    public function getWhereOperators(): array
    {
        return $this->whereOperators;
    }

    public function getWhereCallsCount(): int
    {
        return $this->whereCallsCount;
    }

    public function setWhereCallsCount($count): void
    {
        $this->whereCallsCount = $count;
    }

    public function getSeparator(): string
    {
        return $this->separator;
    }

    public function setSeparator(string $separator): void
    {
        $this->separator = $separator;
    }

    public function incrementWhereCallsCount(): void
    {
        $this->whereCallsCount++;
    }

    public function getUniqueHash(): string
    {
        return $this->uniqueHash;
    }

    public function setUniqueHash(mixed $hash): void
    {
        $this->uniqueHash = $hash;
    }
}