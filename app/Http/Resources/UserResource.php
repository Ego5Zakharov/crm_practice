<?php

namespace App\Http\Resources;

use App\Kernel\Collections\Collection;
use App\Kernel\Database\Model;
use App\Kernel\Json\Resource;

class UserResource extends Resource
{
    public function __construct()
    {
        $resource = new static();

        $resource->setApplicationJsonHeader();
    }

    public static function collection(Collection $data): void
    {
        $data = $data->map(function ($item) {
            return [
                'id' => $item['id'],
                'name' => $item['name'],
            ];
        }, $data);

        echo json_encode($data);
    }

    public static function make(Model|Collection $model)
    {
        $resource = new static();

        $resource->setApplicationJsonHeader();

        if ($model instanceof Model) {
            $model = collect($model);

            $model = $model->map(function ($item) {
                return [
                    'id' => $item['id'],
                    'relations' => collect($item['relations'])->toArray()
                ];
            }, $model)[0];

            echo json_encode($model);
        }

        return Collection::class;
    }
}