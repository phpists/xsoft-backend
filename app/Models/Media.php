<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    use HasFactory;

    /**
     * Type id
     */
    const USER_MEDIA = 1;
    const PRODUCT_MEDIA = 2;

    protected $table = 'media';
    protected $fillable = [
        'type_id',
        'parent_id',
        'file'
    ];
}
