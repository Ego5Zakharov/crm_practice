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
        $users = User::query()->paginate(
            $this->request->input('per_page'),
            $this->request->input('page')
        );
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
                $users
            ]
        ], 200);
    }


    public function store()
    {

    }
}