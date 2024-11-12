<?php

namespace App\Http\Resources\User;

use App\Http\Resources\HasPaginatorResourceCollection;
use App\Http\Resources\Traits\HasFullInfoFlag;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UsersResource extends JsonResource
{
    use HasPaginatorResourceCollection, HasFullInfoFlag;

    public function toArray(Request $request): array
    {
        return $this->returnPaginatedResource(function ($item, $key) {
            return new UserResource($item, $this->fullInfo);
        });
    }
}
