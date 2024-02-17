<?php

namespace App\Kernel\Http\Controllers;

use App\Kernel\Controller\Controller;
use App\Kernel\View\ViewNotFoundException;

class TestController extends Controller
{
    /**
     * @throws ViewNotFoundException
     */
    public function index(): string
    {
        return $this->view->view('test', ['test' => []]);
    }

    public function testPost(): string
    {
        return 'POST METHOD';
    }
}