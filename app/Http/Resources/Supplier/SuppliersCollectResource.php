<?php

namespace App\Http\Resources\Supplier;

use App\Http\Resources\HasResourceCollection;
use App\Http\Resources\Traits\HasFullInfoFlag;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SuppliersCollectResource extends JsonResource
{
    use HasResourceCollection, HasFullInfoFlag;

    public function toArray(Request $request): array
    {
        return $this->returnResource(function ($item, $key) {
            return new SupplierResource($item);
        });
    }
}
