<?php

namespace App\Kernel\Database\Query;

use App\Kernel\Database\Database;
use PDO;
use PDOStatement;

class Builder
{
    protected string $query = "";

    protected PDOStatement $statement;

    protected Database $database;

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

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    // если запрос не пустой - выполяем его, беря query из Builder
    // иначе - null
    public function prepareQuery(): void
    {
        $this->setStatement(
            $this->getQuery()
                ? $this->database->getPDO()->prepare($this->getQuery())
                : null
        );
    }

    // возращает результат текущий класс после выполнения запроса
    public function execute(): static
    {
        if ($this->getBindParams()) {
            $this->getStatement()->execute($this->getBindParams());
        } else {
            $this->getStatement()->execute();
        }

        return $this;
    }

    // возращает результат query-запроса
    public function pureExecute(): bool
    {
        if ($this->getBindParams()) {
            return $this->getStatement()->execute($this->getBindParams());
        } else {
            return $this->getStatement()->execute();
        }
    }

    public function fetch(PDO|int $pdoFetchMode = PDO::FETCH_ASSOC)
    {
        return $this->getStatement()->fetch($pdoFetchMode);
    }

    public function fetchAll(PDO|int $pdoFetchMode = PDO::FETCH_ASSOC): false|array
    {
        return $this->getStatement()->fetchAll($pdoFetchMode);
    }


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

    public function getFirstBindParam()
    {
        return array_values($this->bindParams)[0] ?? [];
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

    public function getStatement(): ?PDOStatement
    {
        return $this->statement;
    }

    public function setStatement(?PDOStatement $statement): void
    {
        $this->statement = $statement;
    }

    public function getLimitCount(): ?int
    {
        return $this->limitCount;
    }

    public function setLimitCount(?int $limitCount): void
    {
        $this->limitCount = $limitCount;
    }

    public function getUniqueHash(): string
    {
        return $this->uniqueHash;
    }

    public function getDatabase(): Database
    {
        return $this->database;
    }

    public function setUniqueHash(mixed $hash): void
    {
        $this->uniqueHash = $hash;
    }
}