<?php

namespace App\Kernel\Database;

use http\Exception\RuntimeException;
use PDO;

class Database
{
    public static PDO $pdo;
    private static ?Database $instance = null;

    public function __construct(
        string $host = 'localhost',
        string $dbname = '',
        string $port = '3306',
        string $username = 'root',
        string $password = 'root')
    {
        $this->connect($host, $dbname, $port, $username, $password);
    }

    private function connect(
        string $host,
        string $dbname,
        string $port,
        string $username,
        string $password): void
    {
        try {
            $this::$pdo = new PDO(
                dsn: "mysql:host=$host;dbname=$dbname;port=$port",
                username: $username,
                password: $password
            );

            self::$instance = $this;
//            echo "PDO has been successfully launched";
        } catch (\PDOException $exception) {
            echo $exception->getMessage();
        }
    }

    public function getPDO(): PDO
    {
        return self::$pdo;
    }

    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            throw new RuntimeException('Database instance has not been created.');
        }

        return self::$instance;
    }
}
