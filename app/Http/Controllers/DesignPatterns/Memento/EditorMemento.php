<?php

namespace App\Http\Controllers\DesignPatterns\Memento;

class EditorMemento
{
    private $content;

    public function __construct($content)
    {
        $this->content = $content;
    }

    public function getContent()
    {
        return $this->content;
    }
}