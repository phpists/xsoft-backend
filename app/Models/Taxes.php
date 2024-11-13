<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Taxes extends Model
{
    use HasFactory;

    protected $table = 'taxes';
    protected $fillable = [
        'title'
    ];
}
