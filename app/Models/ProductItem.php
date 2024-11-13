<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductItem extends Model
{
    use HasFactory;

    protected $table = 'products_item';
    protected $fillable = [
        'product_id',
        'tax_id',
        'cost_price',
        'retail_price'
    ];
}
