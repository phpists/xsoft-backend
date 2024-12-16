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
        $requestParams = request()->all();
        $items = $requestParams['items'] ?? [];

        $currentItem = null;
        foreach ($items as $item) {
            if ($item['id'] === $value) {
                $currentItem = $item;
                break;
            }
        }

        if (empty($currentItem)){
            $this->message = 'Невірна кількість';
            return false;
        }

        $productMovement = ProductsMovementItem::where('id', $currentItem['id'])
            ->where('product_id', $currentItem['product_id'])
            ->where('type_id', ProductMovement::PARISH)
            ->first();

        if (empty($productMovement)) {
            $this->message = 'Невірна кількість';
            return false;
        }

        if ($currentItem['qty'] >= $productMovement->qty) {
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
