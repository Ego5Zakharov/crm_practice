<?php

namespace App\Http\Controllers\API;

use App\Http\Resources\UserResource;
use App\Kernel\Auth\Auth;
use App\Kernel\Controller\Controller;
use App\Kernel\Database\Model;
use App\Kernel\Database\Query\Builder;
use App\Kernel\Json\AnonymousJsonCollection;
use App\Kernel\Json\Response;
use App\Kernel\Request\Request;
use App\Models\Role;
use App\Models\User;

class AdminController extends Controller
{
    public function index(): AnonymousJsonCollection
    {

        $role = Role::query()->create([
            'name' => 'user'
        ]);

        $user = User::query()->create([
            'name' => 'egor',
            'email' => 'egor@mail.ru',
            'password' => Auth::hashPassword(12345678),
            'role_id' => $role->getAttribute('id')
        ]);


//        dd($user->with(['role'])->get());
//        dd(User::query()->with(['role'])->get());

        // тестирование
        // несколько вариантов ответа
        // выбираем вопрос, у вопроса может быть ветка

        $usersWithRoleId100 = User::query()->whereHas('role', function (Model $model) {
            return $model->where('id', '=', 100)
                ->where('name','=','user')
                ->first();
        });

        dd($usersWithRoleId100);
//        return UserResource::collection(
//            User::query()->with(['users'])->get()
//        );
    }
}