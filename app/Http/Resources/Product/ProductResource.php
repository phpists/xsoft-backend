<?php

namespace App\Http\Resources\Product;

use App\Http\Resources\ProductItem\ProductItemsResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $return = [
            'user_id' => $this->user_id,
            'brand_id' => $this->brand_id,
            'category_id' => $this->category_id,
            'article' => $this->article,
            'title' => $this->title,
            'description' => $this->description,
            'product_measure_id' => $this->product_measure_id,
            'color' => $this->color,
            'balance' => $this->balance,
            'materials_used_quantity' => $this->materials_used_quantity,
            'materials_used_measure_id' => $this->materials_used_measure_id,
            'created_at' => date('Y-d-m H:i:s', strtotime($this->created_at)),
            'items' => new ProductItemsResource($this->productItem),
        ];

        return $return;
    }
}
