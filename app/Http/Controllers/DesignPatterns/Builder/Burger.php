<?php

namespace App\Http\Controllers\DesignPatterns\ObjectPool\Builder;

class Burger {
    private $size;
    private $cheese;
    private $pepperoni;
    private $lettuce;
    private $tomato;

    public function setSize($size): void
    {
        $this->size = $size;
    }

    public function setCheese($cheese): void
    {
        $this->cheese = $cheese;
    }

    public function setPepperoni($pepperoni): void
    {
        $this->pepperoni = $pepperoni;
    }

    public function setLettuce($lettuce): void
    {
        $this->lettuce = $lettuce;
    }

    public function setTomato($tomato): void
    {
        $this->tomato = $tomato;
    }

    public function getBurgerInfo(): string
    {
        return "Размер бургера {$this->size}, сыр: {$this->cheese}, пепперони: {$this->pepperoni}, шпинат: {$this->lettuce}, томат: {$this->tomato}\n";
    }
}


