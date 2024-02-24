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



}