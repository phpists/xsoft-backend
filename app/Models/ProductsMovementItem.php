<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductsMovementItem extends Model
{
    use HasFactory;

    protected $table = 'products_movement_item';
    protected $fillable = [
        'product_movement_id',
        'product_id',
        'type_id',
        'qty',
        'measurement_id',
        'cost_price',
        'retail_price',
        'description',
    ];

    public function product()
    {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }
}
