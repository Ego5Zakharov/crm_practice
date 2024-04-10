<?php

namespace App\Http\Controllers\DesignPatterns\ObjectPool\Builder;

class BurgerBuilder
{
    private Burger $burger;

    public function __construct()
    {
        $this->burger = new Burger();
    }

    public function addCheese(): static
    {
        $this->burger->setCheese(true);
        return $this;
    }

    public function addPepperoni(): static
    {
        $this->burger->setPepperoni(true);
        return $this;
    }

    public function addLettuce(): static
    {
        $this->burger->setLettuce(true);
        return $this;
    }

    public function addTomato(): static
    {
        $this->burger->setTomato(true);
        return $this;
    }

    public function setSize($size): static
    {
        $this->burger->setSize($size);
        return $this;
    }

    public function build(): Burger
    {
        return $this->burger;
    }
}