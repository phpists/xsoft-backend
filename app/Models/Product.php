<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';
    protected $fillable = [
        'user_id',
        'brand_id',
        'category_id',
        'article',
        'title',
        'description',
        'product_measure_id',
        'color',
        'balance',
        'materials_used_quantity',
        'materials_used_measure_id'
    ];

    public function productItem()
    {
        return $this->hasMany(ProductItem::class, 'product_id', 'id');
    }

    public function media()
    {
        return $this->hasMany(Media::class, 'parent_id', 'id')
            ->where('type_id', Media::PRODUCT_MEDIA);
    }
}



