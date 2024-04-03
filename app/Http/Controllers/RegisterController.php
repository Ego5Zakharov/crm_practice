<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Kernel\Collections\Collection;
use App\Kernel\Controller\Controller;
use App\Kernel\Database\Model;
use App\Kernel\Database\Query\Exceptions\WhereOperatorNotFoundException;
use App\Kernel\Json\AnonymousJsonCollection;
use App\Kernel\Json\Response;
use App\Models\Role;
use App\Models\User;
use Dotenv\Dotenv;

class RegisterController extends Controller
{
    public function register()
    {

    }
}