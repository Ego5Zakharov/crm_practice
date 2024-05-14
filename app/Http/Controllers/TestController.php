<?php

namespace App\Http\Controllers;

use App\Http\Requests\TestRequest;
use App\Http\Resources\UserResource;
use App\Kernel\Auth\Auth;
use App\Kernel\Collections\Collection;
use App\Kernel\Controller\Controller;
use App\Kernel\Database\Model;
use App\Kernel\Database\Query\Exceptions\WhereOperatorNotFoundException;
use App\Kernel\Json\AnonymousJsonCollection;
use App\Kernel\Json\Response;
use App\Kernel\Request\Request;
use App\Kernel\Throttle\Throtlle;
use App\Kernel\View\ViewNotFoundException;
use App\Models\Role;
use App\Models\User;
use Dotenv\Dotenv;

class TestController extends Controller
{
    /**
     * @throws ViewNotFoundException
     */
    public function testView()
    {
        
        return $this->view->view('test',
            [
                'array' => [
                    1, 2, 3, 4, 5
                ]
            ]);
    }

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

//        $users = User::query()->limit()->where('role_id', '!=', null)->get();

//        return UserResource::collection($users);

        $password = "qwerty123";

        $hashPassword = Auth::hashPassword($password);

        $passwordVerifyResult = Auth::verifyPassword($password, $hashPassword);

        // отправлять запрос
        // отправлять логин пароль
        // находить человека по этому логину
        // если человек найден тогда брать его зашифрованный пароль и входить в аккаунт

        dd($passwordVerifyResult);
//        return UserResource::collection($users);
    }


    public function store(TestRequest $request)
    {
        $validatedData = $request->validated();


    }

    // api show method
    public function show(Request $request)
    {
        $userId = $request->input('userId');

//        $users = User::query()->limit(12)->get()
//            ->sort('id', SORT_ASC)
//            ->map(function ($user) {
//                return [
//                    'id' => $user['id'],
//                    'name' => $user['name'],
//                    'email' => $user['email']
//                ];
//            });


//        $user = User::query()->create([
//            'email' => 'egor@mail.ru',
//            'name' => 123456,
//            'password' => Auth::hashPassword('12345678'),
//        ]);

        $users = User::query()->whereHas('role', function ($query) {
            /**
             * @var Role $query
             */
            return $query->whereHasWhere('name', '=', 'egorAdmin')->first();
        });


        foreach ($users as $user) {
            dump($user);
        }

//        $user = new User();
//        $user->email = "egor@mail.ru";
//        $user->name = "1234567";
//        $user->password = Auth::hashPassword('12345678');
//
//        $user = $user->create();

//        dd(collect($users)->map(function ($user) {
//            dd($user);
//            return [
//                'id' => $user['id']
//            ];
//        }));
    }
}