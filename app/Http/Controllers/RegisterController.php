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
use GrahamCampbell\ResultType\Success;

class RegisterController extends Controller
{
    /**
     * @throws ViewNotFoundException
     */
    public function registerView()
    {
        return $this->view('register/register', []);
    }

    public function register(): void
    {
        $email = request()->input('email');
        $password = request()->input('password');

        if (User::query()->where('email', '=', $email)->first()) {
            echo "fail";
            return;
        }

        User::query()->create([
            'email' => $email,
            'name' => 'egor',
            'password' => Auth::hashPassword($password)
        ]);

        echo "Success!";
    }
}