<?php

namespace App\Kernel\Controller;


use App\Kernel\Database\Database;
use App\Kernel\Request\Request;
use App\Kernel\Session\Session;
use App\Kernel\View\View;
use App\Kernel\View\ViewNotFoundException;

abstract class Controller
{
    public Request $request;
    public View $view;
    public Session $session;
    public Database $database;

    public function __construct(Request $request, View $view, Session $session, Database $database)
    {
        $this->request = $request;
        $this->view = $view;
        $this->session = $session;
        $this->database = $database;
    }

    /**
     * @throws ViewNotFoundException
     */
    public function view(string $filePath, mixed $data = [])
    {
        return $this->view->view($filePath, $data);
    }

    public function request(): Request
    {
        return $this->request;
    }
}