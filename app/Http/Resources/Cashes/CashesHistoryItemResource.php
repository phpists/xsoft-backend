<?php

namespace App\Http\Resources\Cashes;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CashesHistoryItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $return = [
            'id' => $this->id,
            'product_movement_id' => $this->product_movement_id,
            'user_id' => $this->user_id,
            'cashes_id' => $this->cashes_id,
            'type_id' => $this->type_id,
            'type_title' => $this->getTypeTitle(),
            'amount' => $this->amount,
            'amount_cashes' => $this->amount_cashes,
            'user' => $this->user,
            'cashes' => $this->cashes,
            'created_at' => Carbon::parse($this->created_at)->format('d.m.Y H:i:s'),
            'updated_at' => Carbon::parse($this->updated_at)->format('d.m.Y H:i:s'),
        ];

        return $return;
    }
}
