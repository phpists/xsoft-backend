<?php

namespace App\Http\Resources\Cashes;

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
            'user_id' => $this->user_id,
            'cashes_id' => $this->cashes_id,
            'type_id' => $this->getTypeTitle(),
            'amount' => $this->amount,
            'amount_cashes' => $this->amount_cashes,
            'user' => $this->user,
            'cashes' => $this->cashes,
        ];

        return $return;
    }
}
