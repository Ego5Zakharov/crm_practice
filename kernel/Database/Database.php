<?php

namespace App\Kernel\Database;

use PDO;

class Database
{
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
            $pdo = new PDO(
                dsn: "mysql:host=$host;dbname=$dbname;port=$port",
                username: $username,
                password: $password
            );

//            echo "PDO has been successfully launched";
        } catch (\PDOException $exception) {
            echo $exception->getMessage();
        }
    }
}