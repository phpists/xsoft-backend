<?php

namespace App\Http\Resources\Product;

use App\Http\Resources\Media\MediasResource;
use App\Http\Resources\ProductItem\ProductItemsResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $return = [
            'id' => $this->id,
            'company_id' => $this->company_id,
            'user_id' => $this->user_id,
            'brand_id' => $this->brand_id,
            'category_id' => $this->category_id,
            'article' => $this->article,
            'title' => $this->title,
            'description' => $this->description,
            'product_measure_id' => $this->product_measure_id,
            'color' => $this->color,
            'balance' => $this->balance,
            'cost_price' => $this->cost_price,
            'retail_price' => $this->retail_price,
            'materials_used_quantity' => $this->materials_used_quantity,
            'materials_used_measure_id' => $this->materials_used_measure_id,
            'created_at' => date('Y-d-m H:i:s', strtotime($this->created_at)),
            'items' => new ProductItemsResource($this->productItem),
            'media' => new MediasResource($this->media),
            'tags' => json_decode($this->tags),
            'vendors' => json_decode($this->vendors),
        ];

        return $return;
    }
}

