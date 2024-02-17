<?php

namespace App\Kernel\Controller;


use App\Kernel\Request\Request;
use App\Kernel\View\View;

abstract class Controller
{
    public Request $request;
    public View $view;

    public function __construct(Request $request, View $view)
    {
        $this->request = $request;
        $this->view = $view;
    }

}