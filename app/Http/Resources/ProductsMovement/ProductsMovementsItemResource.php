<?php

namespace App\Http\Resources\ProductsMovement;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductsMovementsItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $return = [
            'id' => $this->id,
            'product_movement_id' => $this->product_movement_id,
            'product_id' => $this->product_id,
            'type_id' => $this->type_id,
            'qty' => $this->qty,
            'measurement_id' => $this->measurement_id,
            'cost_price' => $this->cost_price,
            'retail_price' => $this->retail_price,
            'created_at' => Carbon::parse($this->created_at)->format('Y-m-d H:i:s')
        ];

        return $return;
    }
}
