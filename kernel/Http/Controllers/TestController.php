<?php

namespace App\Kernel\Http\Controllers;

class TestController
{
    public function testGet(): string
    {
        return require_once APP_PATH . "/views/pages/test.php";
    }

    public function testPost(): string
    {
        return 'POST METHOD';
//        return require_once APP_PATH . "/views/pages/test.php";
    }
}