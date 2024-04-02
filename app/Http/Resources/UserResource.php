<?php

namespace App\Http\Resources;

use App\Kernel\Collections\Collection;
use App\Kernel\Database\Model;
use App\Kernel\Json\Resource;

class UserResource extends Resource
{
    public function toArray(): array
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'role' => $this->resource->role ? $this->resource->role->toArray() : null,
        ];
    }
}