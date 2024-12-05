<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductMovement extends Model
{
    use HasFactory;

    /**
     * Обіг товарів
     */

    /**
     * Type
     */
    const PARISH = 1; // Прихід
    const SALE = 2; // Продаж
    const WRITE_DOWN = 3; // Списання

    protected $table = 'products_movement';
    protected $fillable = [
        'company_id',
        'staff_id',
        'warehouse_id',
        'supplier_id',
        'box_office_id',
        'type_id',
        'total_price',
        'date_create',
        'time_create',
        'debt',
        'installment_payment',
        'box_office_date'
    ];

    public function getTypeTitle()
    {
        $title = '';
        switch ($this->type_id) {
            case self::PARISH;
                $title = 'Прихід';
                break;
            case self::SALE;
                $title = 'Продаж';
                break;
            case self::WRITE_DOWN;
                $title = 'Списання';
                break;
        }

        return $title;
    }

    public function items()
    {
        return $this->hasMany(ProductsMovementItem::class, 'product_movement_id', 'id');
    }
}
