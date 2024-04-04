<?php

namespace App\Kernel\Middlewares;

use App\Kernel\Request\Request;
use JetBrains\PhpStorm\NoReturn;

abstract class Middleware
{
    public abstract function handle();

    #[NoReturn] public function httpError($status = 400, $data = []): void
    {
        http_response_code($status);
        header('Content-type: application/json');
        echo json_encode($data);
        exit;
    }
}