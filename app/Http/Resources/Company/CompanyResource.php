<?php

namespace App\Http\Resources\Company;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $return = [
            'id' => $this->id,
            'title' => $this->title,
            'user_id' => $this->user_id,
            'color' => $this->color,
            'category_id' => $this->category_id,
            'created_at' => Carbon::parse($this->created_at)->format('Y-m-d H:i:s'),
            'branches' => $this->branches,
        ];

        return $return;
    }
}
