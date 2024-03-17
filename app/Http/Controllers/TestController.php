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
        // написать пагинацию
        // написать ресурсы для маппинга нужных данных
        // написать метод with

        $users = User::query()->limit(100)->get()->toArray();

        $paginator = new LengthAwarePaginator(
            $users,
            $this->request->input('per_page'),
            $this->request->input('page')
        );

        return response()->json([
            'data' => [
                $paginator->getItems(),
                $paginator->getInfo()
            ]
        ], 200);
    }


    public function store()
    {

    }
}