<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    /**
     * Посади
     */
    use HasFactory;

    protected $table = 'positions';
    protected $fillable = [
      'title'
    ];
}
