<?php

namespace App\Http\Resources\ProductItem;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $return = [
            'product_id' => $this->product_id,
            'tax_id' => $this->tax_id,
            'cost_price' => $this->cost_price,
            'retail_price' => $this->retail_price,
        ];

        return $return;
    }
}
