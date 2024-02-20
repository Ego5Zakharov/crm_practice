<?php

namespace App\Kernel\Http\Controllers;

use App\Kernel\Controller\Controller;
use App\Kernel\View\ViewNotFoundException;
use JetBrains\PhpStorm\NoReturn;

class TestController extends Controller
{
    /**
     * @throws ViewNotFoundException
     */
    public function index(): string
    {
        return $this->view->view('test', ['test' => []]);
    }

    /**
     * @throws ViewNotFoundException
     */
    public function create(): string
    {
        return $this->view->view('test/create');
    }

    public function store(): string
    {
//        dd($this->request->input('name'));


        return 'POST METHOD';
    }
}