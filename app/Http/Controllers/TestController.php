<?php

namespace App\Http\Controllers;

use App\Kernel\Controller\Controller;
use App\Kernel\Json\Response;
use App\Kernel\Pagination\LengthAwarePaginator;
use App\Models\User;
use Dotenv\Dotenv;

class TestController extends Controller
{
    public function index(): Response
    {
        $user = new User();

        $user->setAttribute('name', 'Egor');
        $user->setAttribute('email', 'egor@mail.ru');
        $user->setAttribute('password', '12345678');

        $user = $user->create();

        $user->setAttribute('email', 'egorUPDATE@mail.ru');

        $user->update();

        $user->freshQuery();

        dd($user->where('id','=','2118')->first());

//        dd($user->delete());


        // написать ресурсы для маппинга нужных данных
        // написать метод with
//        $users = User::query()->paginate();

//        $paginator = new LengthAwarePaginator(
//            $users,
//            $this->request->input('per_page'),
//            $this->request->input('page')
//        );
//
        return response()->json([
            'data' => [
//                $users
            ]
        ], 200);
    }


    public function store()
    {

    }
}