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

}