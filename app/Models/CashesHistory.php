<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashesHistory extends Model
{
    use HasFactory;

    protected $table = 'cashes_history';
    protected $fillable = [
        'user_id',
        'cashes_id',
        'type_id',
        'amount',
        'amount_cashes'
    ];
}
