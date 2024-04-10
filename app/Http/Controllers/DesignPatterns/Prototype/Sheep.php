<?php

namespace App\Http\Controllers\DesignPatterns\Prototype;

class Sheep
{
    protected string $name;
    protected string $category;

    public function __construct(string $name, string $category = 'Mountain Sheep')
    {
        $this->name = $name;
        $this->category = $category;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function __clone()
    {
        $this->name = 'Clone of ' . $this->name;
    }
}