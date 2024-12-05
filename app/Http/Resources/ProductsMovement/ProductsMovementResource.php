<?php

namespace App\Http\Resources\ProductsMovement;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductsMovementResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $return = [
            'id' => $this->id,
            'company_id' => $this->company_id,
            'staff_id' => $this->staff_id,
            'warehouse_id' => $this->warehouse_id,
            'supplier_id' => $this->supplier_id,
            'type_id' => $this->type_id,
            'date_create' => $this->date_create,
            'time_create' => $this->time_create,
            'debt' => $this->debt,
            'installment_payment' => $this->installment_payment,
            'box_office_date' => $this->box_office_date,
            'total_price' => $this->total_price,
            'created_at' => Carbon::parse($this->created_at)->format('Y-m-d H:i:s'),

            'items' => new ProductsMovementsItemsResource($this->items)
        ];

        return $return;
    }
}
