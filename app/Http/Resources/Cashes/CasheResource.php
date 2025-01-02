<?php

namespace App\Http\Resources\Cashes;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CasheResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $return = [
            'id' => $this->id,
            'company_id' => $this->company_id,
            'title' => $this->title,
            'appointment' => $this->appointment,
            'description' => $this->description,
            'is_cash_category' => $this->is_cash_category,
            'total' => $this->total,
            'debt' => $this->debt,
            'created_at' => Carbon::parse($this->created_at)->format('d.m.Y H:i:s'),
            'cash_categories' => $this->cashCategories,
        ];

        return $return;
    }
}
