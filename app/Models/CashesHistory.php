<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashesHistory extends Model
{
    use HasFactory;

    protected $table = 'cashes_history';
    protected $fillable = [
        'product_movement_id',
        'user_id',
        'cashes_id',
        'type_id',
        'amount',
        'amount_cashes'
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function cashes()
    {
        return $this->hasOne(Cashes::class, 'id', 'cashes_id');
    }

    public function getTypeTitle()
    {
        $title = '';
        switch ($this->type_id) {
            case ProductMovement::PARISH;
                $title = 'Прихід';
                break;
            case ProductMovement::SALE;
                $title = 'Продаж';
                break;
            case ProductMovement::WRITE_DOWN;
                $title = 'Списання';
                break;
            case ProductMovement::DEBT;
                $title = 'Борг';
                break;
        }

        return $title;
    }
}
