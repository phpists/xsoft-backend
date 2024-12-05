<?php

namespace App\Http\Resources\Warehouse;

use App\Http\Resources\HasPaginatorResourceCollection;
use App\Http\Resources\HasResourceCollection;
use App\Http\Resources\Traits\HasFullInfoFlag;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WarehousesResource extends JsonResource
{
    use HasResourceCollection, HasFullInfoFlag;

    public function toArray(Request $request): array
    {
        return $this->returnResource(function ($item) {
            return new WarehouseResource($item);
        });
    }
}
