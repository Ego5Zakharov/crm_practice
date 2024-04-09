<?php

namespace App\Kernel\Json;

use App\Kernel\Collections\Collection;
use App\Kernel\Database\Model;
use App\Kernel\Pagination\LengthAwarePaginator;

class Resource
{
    public Collection|array $items = [];
    public mixed $resource;
    public array $with = [];
    public array $headers = [];
    public int $status = 200;

    public static function collection(Collection|LengthAwarePaginator $models): AnonymousJsonCollection
    {
        $classPath = static::class;

        $resource = new $classPath();

        $resourceItems = [];

        if ($models instanceof Collection) {

            foreach ($models->toArray() as $index => $model) {

                $resource->resource = collect($model);

                $resourceItems[$index] = $resource->toArray();
            }
        } else if ($models instanceof LengthAwarePaginator) {
            foreach ($models->getItems() as $index => $model) {
                $resource->resource = collect($model);
                $resourceItems['items'][$index] = $model;
            }
            $resourceItems['meta'] = $models->getMeta();
        }


        $resource->setApplicationJsonHeader();

        echo json_encode($resourceItems);

        return new AnonymousJsonCollection($resourceItems);
    }


    public static function make(Model $model): Resource|array|null
    {
        $classPath = static::class;

        $resource = new $classPath();

        $resource->resource = $model;

        $resource->setApplicationJsonHeader();

        $result = $resource->toArray();

        echo json_encode($result);

        return $resource;
    }

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

    // переопределенный метод
    public function toArray(): array
    {
        return [];
    }

}