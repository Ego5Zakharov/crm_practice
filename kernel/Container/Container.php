<?php

namespace App\Kernel\Container;

use App\Kernel\Database\Database;
use App\Kernel\Request\Request;
use App\Kernel\Router\Router;

class Container
{
    public Database $database;
    public Router $router;
    public Request $request;

    public function __construct()
    {
        $this->initialize();
    }

    public function initialize(): void
    {
        $this->request = Request::initialization();

//        $this->database = new Database('localhost', 'crm_practice', '3306', 'root', 'root');

        $this->router = new Router();
    }
}