<?php

namespace App\Kernel\Container;

use App\Kernel\Config\Config;
use App\Kernel\Controller\Controller;
use App\Kernel\Database\Database;
use App\Kernel\Http\Controllers\TestController;
use App\Kernel\Request\Request;
use App\Kernel\Router\Router;
use App\Kernel\Session\Session;
use App\Kernel\View\View;

class Container
{
    public Database $database;
    public Router $router;
    public Request $request;
    public Config $config;
    public View $view;
    public Session $session;

    public function __construct()
    {
        $this->initialize();
    }

    public function initialize(): void
    {
        $this->config = new Config();

        $this->view = new View();

        $this->request = Request::initialization();

        $this->session = new Session();

        $this->database = new Database(
            $this->config->get('database.host'),
            $this->config->get('database.dbname'),
            $this->config->get('database.port'),
            $this->config->get('database.username'),
            $this->config->get('database.password')
        );

        $this->router = new Router(
            $this->request,
            $this->view,
            $this->session

        );
    }
}