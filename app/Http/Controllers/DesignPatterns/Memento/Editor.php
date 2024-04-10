<?php

namespace App\Http\Controllers\DesignPatterns\Memento;

class Editor
{
    private $content = '';

    public function type($words)
    {
        $this->content .= ' ' . $words;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function save()
    {
        return new EditorMemento($this->content);
    }

    public function restore(EditorMemento $memento)
    {
        $this->content = $memento->getContent();
    }
}