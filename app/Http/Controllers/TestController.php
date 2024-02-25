<?php

namespace App\Http\Controllers;

use App\Kernel\Controller\Controller;
use App\Kernel\View\ViewNotFoundException;
use App\Models\Role;
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

    public function store()
    {
//        dd($this->request->input('name'));

//        $user = new User();
//
//        $user->name = "Egor";
//        $user->email = "egor@mail.ru";
//        $user->password = "12345678";

//        $user = $user->find(80);
//        dd($user->name);

//        $user = $user->create();
//        dd($user);

//
//        $role = new Role();
//        $role->name = "admin";
//        $role->create();
//
//        $user->id = 10;
//
//        $user->update();

//        $orm = new ORM($this->database->getPDO());





        $role = new Role();
        $role->name = "admin";
        $role->create();
        $user = new User();

        $user->name = "Egor";
        $user->email = "egor@mail.ru";
        $user->password = "password";
        $user->role_id = $role->id;
        $user->create();

        // как получить role_id?
        dd($user->role());
//        $user->hasOne(Role::class, "role_id", 'id');
    }
}