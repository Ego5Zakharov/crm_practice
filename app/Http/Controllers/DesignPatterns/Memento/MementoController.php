<?php

namespace App\Http\Controllers\DesignPatterns\Memento;

class MementoController
{
    public function handle(): void
    {
        $editor = new Editor();

        $editor->type('Это первый снимок.');
        $savedState = $editor->save();

        $editor->type('Это второй снимок.');
        echo $editor->getContent();

        $editor->restore($savedState);
        echo $editor->getContent();
    }
}

