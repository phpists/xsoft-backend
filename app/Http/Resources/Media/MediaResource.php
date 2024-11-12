<?php

namespace App\Http\Resources\Media;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MediaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $return = [
            'id' => $this->id,
            'type_id' => $this->type_id,
            'parent_id' => $this->parent_id,
            'file' => asset("uploads/media/{$this->file}")
        ];

        return $return;
    }
}
