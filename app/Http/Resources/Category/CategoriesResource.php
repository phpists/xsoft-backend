<?php

namespace App\Http\Resources\Category;

use App\Http\Resources\Company\CompanyResource;
use App\Http\Resources\HasResourceCollection;
use App\Http\Resources\Traits\HasFullInfoFlag;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoriesResource extends JsonResource
{
    use HasResourceCollection, HasFullInfoFlag;

    public function toArray(Request $request): array
    {
        return $this->returnResource(function ($item, $key) {
            return new CompanyResource($item);
        });
    }
}
