<?php

namespace App\Http\Resources\Supplier;

use App\Http\Resources\HasPaginatorResourceCollection;
use App\Http\Resources\Traits\HasFullInfoFlag;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SuppliersResource extends JsonResource
{
    use HasPaginatorResourceCollection, HasFullInfoFlag;

    public function toArray(Request $request): array
    {
        return $this->returnPaginatedResource(function ($item, $key) {
            return new SupplierResource($item, $this->fullInfo);
        });
    }
}
