<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Kernel\Collections\Collection;
use App\Kernel\Controller\Controller;
use App\Kernel\Database\Model;
use App\Kernel\Database\Query\Exceptions\WhereOperatorNotFoundException;
use App\Kernel\Json\Response;
use App\Models\Role;
use App\Models\User;
use Dotenv\Dotenv;

class TestController extends Controller
{

    public function index()
    {
//        $user = User::query()->create([
//            'name' => 'egor',
//            'email' => 'egor@email.ru',
//            'password' => 12345678,
//        ]);
//
//        $users = $user->newQuery()->get();
//        dump($users);

//        dd($users);


//
//        $user->update([
//            'name' => 'egorUpdate12',
//            'email' => 'egorUpdate12@email.ru',
//            'password' => 'updatedPassword12',
//            'role_id' => Role::query()->where('name', '=', 'admin')->first()->getAttribute('id')
//        ]);
//
//        dd(123);

//        $users = User::query()
//            ->limit(12)
//            ->get();
//
//        $users = $users->map(function ($item) {
//            return [
//                'id' => $item['id'],
//                'email' => $item['email']
//            ];
//        }, $users);
//
//        dd($users);
//        $users = collect()->map(function ($item = 2, $key = 1) {
//            return 1 * 2;
//        }, $users);

//        dd($users);


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
//        return response()->json([
//            'data' => [
//
//            ]
//        ], 200);

//        dd(Role::query()->get());

//        $role = Role::query()->create([
//            'name' => 'egorAdmin'
//        ]);
//
//        $user = User::query()->create([
//            'name' => 'egor',
//            'email' => 'egor@email.ru',
//            'password' => 12345678,
//            'role_id' => $role->getAttribute('id')
//        ])->fresh();

        $users = User::query()->limit()->where('role_id', '!=', null)->get();

//        return UserResource::collection($users);

        return UserResource::collection($users);
    }


    public function store()
    {

    }
}