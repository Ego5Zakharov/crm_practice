<?php

namespace App\Http\Controllers;

use App\Kernel\Controller\Controller;
use App\Kernel\View\ViewNotFoundException;
use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;

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
        $role1 = new Role();
        $role1->name = "admin";
        $role1->create();

        $role2 = new Role();
        $role2->name = 'user';
        $role2->create();

        $user1 = new User();

        $user1->name = "Egor";
        $user1->email = "egor@mail.ru";
        $user1->password = "password";
        $user1->create();

        $user2 = new User();
        $user2->name = "Egor";
        $user2->email = "egor@mail.ru";
        $user2->password = "password";

        $user2->create();

        $user3 = new User();
        $user3->name = "Egor";
        $user3->email = "egor@mail.ru";
        $user3->password = "password";
        $user3->create();


        $userRole = new UserRole();
        $userRole->user_id = $user1->id;
        $userRole->role_id = $role1->id;
        $userRole->create();

        $userRole2 = new UserRole();
        $userRole2->user_id = $user1->id;
        $userRole2->role_id = $role2->id;
        $userRole2->create();

        $userRole3 = new UserRole();
        $userRole3->user_id = $user3->id;
        $userRole3->role_id = $role1->id;
        $userRole3->create();

//        dd($role1->users());

        dd($user1->where('id', '!=', 925));

//        dd($user1->roles()) ;


//        dd($user->role());

//        dump($role->users);

//      $user->hasOne(Role::class, "role_id", 'id');
    }
}