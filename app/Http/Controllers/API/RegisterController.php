<?php

namespace App\Http\Controllers\API;

use App\Kernel\Auth\Auth;
use App\Kernel\Controller\Controller;
use App\Models\User;

class RegisterController extends Controller
{
    public function register()
    {
        $email = request()->input('email');
        $password = request()->input('password');

//        if (User::query()->where('email', '=', $email)->first()) {
//            return response()->json([
//                'Email already used.'
//            ]);
//        }

        $user = User::query()->create([
            'email' => $email,
            'name' => 'egor',
            'password' => Auth::hashPassword($password)
        ]);

        $user->createToken();

        return response()->json([
            'user' => $user,
            'token' => $user->getToken(),
        ]);
    }
}