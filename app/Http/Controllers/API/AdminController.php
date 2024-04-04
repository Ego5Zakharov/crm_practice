<?php

namespace App\Http\Controllers\API;

use App\Http\Resources\UserResource;
use App\Kernel\Auth\Auth;
use App\Kernel\Controller\Controller;
use App\Kernel\Json\AnonymousJsonCollection;
use App\Kernel\Json\Response;
use App\Kernel\Request\Request;
use App\Models\User;

class AdminController extends Controller
{
    public function index(): AnonymousJsonCollection
    {
        return UserResource::collection(
            User::query()->get()
        );
    }
}