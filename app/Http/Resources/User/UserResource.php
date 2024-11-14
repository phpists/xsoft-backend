<?php

namespace App\Http\Resources\User;

use App\Http\Resources\Media\MediaResource;
use App\Http\Resources\Media\MediasResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $return = [
            'id' => $this->id,
            'parent_id' => $this->parent_id,
            'category_id' => $this->category_id,
            'role_id' => $this->role_id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'phones' => json_decode($this->phones),
            'color' => $this->color,
            'bd_date' => $this->bd_date,
            'comment' => $this->comment,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'media' => new MediasResource($this->media)
        ];

        return $return;
    }
}
