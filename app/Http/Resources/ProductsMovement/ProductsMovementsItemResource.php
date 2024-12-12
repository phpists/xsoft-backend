<?php

namespace App\Http\Resources\ProductsMovement;

use App\Http\Resources\Product\ProductResource;
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
            'type_title' => $this->getTypeTitle(),
            'qty' => $this->qty,
            'measurement_id' => $this->measurement_id,
            'cost_price' => $this->cost_price,
            'retail_price' => $this->retail_price,
            'description' => $this->description,
            'created_at' => Carbon::parse($this->created_at)->format('d.m.Y H:i:s'),

            'product' => new ProductResource($this->product)
        ];

        return $return;
    }
}
