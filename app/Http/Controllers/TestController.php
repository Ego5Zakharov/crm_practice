<?php

namespace App\Http\Controllers;

use App\Kernel\Controller\Controller;
use App\Kernel\Database\ORM;
use App\Kernel\View\ViewNotFoundException;
use App\Models\User;

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

        $user = new User();

        $user->name = "Egor";
        $user->email = "egor@mail.ru";
        $user->password = "12345678";

        $orm = new ORM($this->database->getPDO());

        $result = $orm->transaction(function () use ($orm, $user) {
            return $orm->create($user, 'users');
        });

        dd($result);

        return 'POST METHOD';
    }
}