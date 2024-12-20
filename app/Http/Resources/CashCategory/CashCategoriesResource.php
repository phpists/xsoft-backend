<?php

namespace App\Http\Resources\CashCategory;

use App\Http\Resources\HasResourceCollection;
use App\Http\Resources\Traits\HasFullInfoFlag;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CashCategoriesResource extends JsonResource
{
    use HasResourceCollection, HasFullInfoFlag;

    public function toArray(Request $request): array
    {
        return $this->returnResource(function ($item, $key) {
            return new CashCategoryResource($item);
        });
    }
}
