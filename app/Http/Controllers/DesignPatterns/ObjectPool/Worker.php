<?php

namespace App\Http\Controllers\DesignPatterns\ObjectPool\ObjectPool;

class Worker
{
    private int $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function getId(): int
    {
        return $this->id;
    }
}