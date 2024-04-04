<?php

namespace App\Http\Controllers\API;

use App\Kernel\Auth\Auth;
use App\Kernel\Controller\Controller;
use App\Kernel\Json\Response;
use App\Models\User;
use Exception;

class RegisterController extends Controller
{
    /**
     * @throws Exception
     */
    public function register(): Response
    {
        $email = request()->input('email');
        $password = request()->input('password');

        if (User::query()->where('email', '=', $email)->first()) {
            return response()->json([
                'Email already used.'
            ]);
        }

        $user = User::query()->create([
            'email' => $email,
            'name' => 'egor',
            'password' => Auth::hashPassword($password)
        ]);

        $user->createToken();

        return response()->json([
            'user' => $user->toArray(),
            'token' => $user->getToken(),
        ]);
    }
}