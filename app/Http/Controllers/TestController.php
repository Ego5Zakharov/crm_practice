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
        $role = new Role();
        $role->name = "admin";
        $role->create();

        $role2 = new Role();
        $role2->name = 'user';
        $role2->create();

        $user1 = new User();

        $user1->name = "Egor";
        $user1->email = "egor@mail.ru";
        $user1->password = "password";
        $user1->role_id = $role->id;
        $user1->create();

        $user2 = new User();
        $user2->name = "Egor";
        $user2->email = "egor@mail.ru";
        $user2->password = "password";
        $user2->role_id = $role->id;
        $user2->create();

        $user3 = new User();
        $user3->name = "Egor";
        $user3->email = "egor@mail.ru";
        $user3->password = "password";
        $user3->role_id = $role->id;
        $user3->create();

//        dd($user->role());

//        dump($role->users);

//      $user->hasOne(Role::class, "role_id", 'id');
    }
}