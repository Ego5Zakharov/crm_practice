<?php

namespace App\Kernel\Container;

use App\Kernel\Config\Config;
use App\Kernel\Database\Database;
use App\Kernel\Request\Request;
use App\Kernel\Router\Router;

class Container
{
    public Database $database;
    public Router $router;
    public Request $request;
    public Config $config;

    public function __construct()
    {
        $this->initialize();
    }

    public function initialize(): void
    {
        $this->request = Request::initialization();

        $this->config = new Config();

        $this->database = new Database(
            $this->config->get('database.host'),
            $this->config->get('database.dbname'),
            $this->config->get('database.port', '3306'), // Указываем значение по умолчанию
            $this->config->get('database.username'),
            $this->config->get('database.password')
        );


        $this->router = new Router();
    }
}