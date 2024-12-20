<?php

namespace App\Http\Resources\Brand;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BrandResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $return = [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'color' => $this->color,
            'created_at' => Carbon::parse($this->created_at)->format('d.m.Y H:i:s'),
        ];

        return $return;
    }
}
