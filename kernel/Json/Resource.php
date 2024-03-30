<?php

namespace App\Kernel\Json;

use App\Kernel\Collections\Collection;

abstract class Resource
{
    protected Collection|array $data = [];

    protected array $headers = [];

    protected int $status = 200;

    public function setHeaders(array $headers): void
    {
        $this->headers = $headers;

        foreach ($headers as $header) {
            header($header);
        }
    }

    public function setApplicationJsonHeader(): void
    {
        $this->setHeaders(array_merge($this->headers, ['Content-type: application/json']));
    }
}