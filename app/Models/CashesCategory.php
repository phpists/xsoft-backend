<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashesCategory extends Model
{
    use HasFactory;

    protected $table = 'cashes_categories';
    protected $fillable = [
        'cashes_id',
        'cash_category_id'
    ];

    public function category()
    {
        return $this->hasOne(CashCategory::class, 'id', 'cash_category_id');
    }
}

