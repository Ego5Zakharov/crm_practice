<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Kernel\Auth\Auth;
use App\Kernel\Collections\Collection;
use App\Kernel\Controller\Controller;
use App\Kernel\Database\Model;
use App\Kernel\Database\Query\Exceptions\WhereOperatorNotFoundException;
use App\Kernel\Json\AnonymousJsonCollection;
use App\Kernel\Json\Response;
use App\Kernel\View\ViewNotFoundException;
use App\Models\Role;
use App\Models\User;
use Dotenv\Dotenv;

class LoginController extends Controller
{
    /**
     * @throws ViewNotFoundException
     */
    public function loginView()
    {
        return $this->view('login/login');
    }

    /**
     * @throws ViewNotFoundException
     */
    public function login()
    {
        $email = request()->input('email');
        $password = request()->input('password');

        if (Auth::attempt($email, $password)) {
            return $this->view('/users/dashboard', ['user' => Auth::user()]);
        }

        return $this->view('login/login');
    }


}