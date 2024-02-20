<?php

namespace App\Kernel\Database;

use PDO;

class ORM
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    private function create(Entity $entity, string $table)
    {
        $data = $entity->toArray();



    }

}