<?php

namespace App\Kernel\Container;

use App\Kernel\Database\Database;
use Symfony\Component\VarDumper\Cloner\Data;

class Container
{
    public Database $database;

    public function __construct()
    {
        $this->initialize();
    }

    public function initialize(): void
    {
        $this->database = new Database('localhost', 'crm_practice', '3306', 'root', '');
    }
}