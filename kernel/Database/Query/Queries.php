<?php

namespace App\Kernel\Database\Query;

use PDOStatement;

trait Queries
{
    protected string $query = "";

    protected PDOStatement $statement;

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
}