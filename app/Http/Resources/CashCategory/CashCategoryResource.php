<?php

namespace App\Http\Resources\CashCategory;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CashCategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'company_id' => $this->company_id,
            'title' => $this->title,
            'type_id' => $this->type_id,
            'created_at' => Carbon::parse($this->created_at)->format('d.m.Y H:i:s'),
        ];
    }
}
