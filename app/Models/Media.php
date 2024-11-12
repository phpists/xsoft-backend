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

    protected $table = 'media';
    protected $fillable = [
        'type_id',
        'parent_id',
        'file'
    ];
}
