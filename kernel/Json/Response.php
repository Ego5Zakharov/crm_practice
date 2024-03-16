<?php

namespace App\Kernel\Json;

class Response
{
    public function __construct(
        protected array $data = [],
        protected array $headers = [],
        protected array $options = [],
        protected int   $status = 200,
    )
    {

    }

    public function response(): static
    {
        return $this;
    }

    public function json(
        array $data = [],
        int   $status = 200,
        array $options = [],
        array $headers = []): static
    {

        $this->setData($data);

        $this->setHeaders(
            array_merge(['Content-type: application/json'], $headers)
        );

        $this->setStatus($status);

        $this->setOptions($options);

        echo json_encode($this->getData());

        return $this;
    }


    public function getData(): array
    {
        return $this->data;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function setData(array $data = []): array
    {
        return $this->data = $data;
    }

    public function setHeaders(array $headers): void
    {
        $this->headers = $headers;

        foreach ($headers as $header) {
            header($header);
        }
    }

    public function setOptions(array $options): void
    {
        $this->options = $options;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function setStatus(int $status): void
    {
        $this->status = $status;
    }


}