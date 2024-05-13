<?php

namespace App\Kernel\Request\Rules;

abstract class Rule
{
    public string $feedback = "";

    // экземпляр объекта
    public ?Rule $instance = null;

    public function setFeedback(string $value): void
    {
        $this->feedback = $value;
    }

    // возвращает сообщение ошибки
    public abstract static function getFeedback(string $valueName): string;
}