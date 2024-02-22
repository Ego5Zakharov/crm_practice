<?php

namespace App\Kernel\Database;

use Exception;
use PDO;
use PDOException;

class ORM
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @throws PDOException
     */
    public function create(Model $entity, string $table)
    {
        $data = $entity->toArray();

        $columns = implode(', ', array_keys($data));

        $placeholders = ':' . implode(', :', array_keys($data));

        $query = "INSERT INTO $table ($columns) VALUES ($placeholders)";

        $statement = $this->pdo->prepare($query);

        $statement->execute($data);

        return $this->findById($this->pdo->lastInsertId(), $table);
    }

    public function transaction(callable $callback)
    {
        try {
            $this->pdo->beginTransaction();

            $result = $callback();

            $this->pdo->commit();

            return $result;
        } catch (PDOException $exception) {

            $this->pdo->rollBack();

            throw new PDOException($exception);
        }
    }

    public function findById(int $id, string $table)
    {
        $query = "SELECT * FROM $table WHERE id = :id";

        $statement = $this->pdo->prepare($query);

        $statement->bindParam('id', $id);

        return $statement->execute() ? $statement->fetch(PDO::FETCH_ASSOC) : null;
    }

}