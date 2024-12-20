<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashCategory extends Model
{
    use HasFactory;

    protected $table = 'cash_categories';
    protected $fillable = [
        'company_id',
        'title',
        'type_id'
    ];
}
