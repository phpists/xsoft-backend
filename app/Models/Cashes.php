<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cashes extends Model
{
    use HasFactory;

    protected $table = 'cashes';
    protected $fillable = [
        'company_id',
        'title',
        'appointment',
        'description',
        'is_cash_category',
    ];

    public function cashCategories()
    {
        return $this->hasMany(CashesCategory::class, 'cashes_id', 'id')
            ->with('category');
    }
}
