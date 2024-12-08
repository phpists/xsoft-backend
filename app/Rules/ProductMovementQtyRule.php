<?php

namespace App\Rules;

use App\Models\ProductMovement;
use App\Models\ProductsMovementItem;
use Closure;

use Illuminate\Contracts\Validation\Rule;

class ProductMovementQtyRule implements Rule
{
    private $message;

    public function __construct()
    {
        $this->message = 'Invalid value provided.';
    }

    public function passes($attribute, $value)
    {
        $productMovement = ProductsMovementItem::where('product_movement_id', request()->get('product_movement_id'))
            ->where('product_id', request()->get('product_id'))
            ->where('type_id', ProductMovement::PARISH)
            ->first();

        if (empty($productMovement)){
            $this->message = 'Невірна кількість';
            return false;
        }


        if ($value >= $productMovement->qty) {
            $this->message = 'Максимальна кількість товарів на складі: ' . $productMovement->qty;
            return false;
        }

        return true;
    }

    public function message()
    {
        return $this->message;
    }
}
