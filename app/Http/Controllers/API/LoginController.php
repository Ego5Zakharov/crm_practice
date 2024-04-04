<?php

namespace App\Http\Controllers\API;

use App\Kernel\Auth\Auth;
use App\Kernel\Controller\Controller;
use App\Kernel\Json\Response;

class LoginController extends Controller
{
    /**
     * @throws \Exception
     */
    public function login(): Response
    {
        $email = request()->input('email');
        $password = request()->input('password');

        if ($user = Auth::attemptAPI($email, $password)) {

            $user->createToken();

            $token = $user->getToken()->toArray();

            $user = $user->toArray();

            return response()->json([
                'message' => 'success',
                'user' => $user,
                'token' => [
                    'access_token' => $token['access_token'],
                    'expires_at' => $token['expires_at']
                ],
            ]);

        }
        return response()->json([
            'message' => 'Auth error!'
        ]);
    }
}