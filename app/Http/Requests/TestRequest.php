<?php

namespace App\Http\Requests;

use App\Kernel\Request\Request;

class TestRequest extends Request
{
    public function validated(): array
    {
        return $this->validate([
            'email' => [
                'string', 'min:1', 'max:16', 'email'
            ],

            'password' => [
                'string', 'min:8'
            ],
        ]);
    }
}