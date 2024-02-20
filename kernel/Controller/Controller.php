<?php

namespace App\Kernel\Controller;


use App\Kernel\Request\Request;
use App\Kernel\Session\Session;
use App\Kernel\View\View;

abstract class Controller
{
    public Request $request;
    public View $view;
    public Session $session;

    public function __construct(Request $request, View $view, Session $session)
    {
        $this->request = $request;
        $this->view = $view;
        $this->session = $session;
    }

}