<?php

namespace App\Http\Controllers;

use App\Kernel\Controller\Controller;
use App\Kernel\Database\Query\Exceptions\WhereOperatorNotFoundException;
use App\Kernel\Json\Response;
use App\Models\Role;
use App\Models\User;
use Dotenv\Dotenv;

class TestController extends Controller
{

    public function index(): Response
    {
        $user = User::query()->create([
            'name' => 'egor',
            'email' => 'egor@email.ru',
            'password' => 12345678,
        ]);

        $user->update([
            'name' => 'egorUpdate12',
            'email' => 'egorUpdate12@email.ru',
            'password' => 'updatedPassword12',
            'role_id' => Role::query()->where('name', '=', 'admin')->first()->getAttribute('id')
        ]);

        dd($user->newQuery()->limit(12)->get());
//        $user = $user->fresh();

//        $users= $user->newQuery()->paginate();
//
//        dd($users);

//        $users = User::query()->paginate(
//            $this->request->input('per_page'),
//            $this->request->input('page')
//        );

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

            ]
        ], 200);
    }


    public function store()
    {

    }
}