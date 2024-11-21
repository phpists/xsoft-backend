<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $table = 'companies';
    protected $fillable = [
        'title',
        'user_id',
        'category_id',
        'color',
    ];

    public function branches()
    {
        return $this->hasMany(CompanyBranches::class, 'company_id', 'id');
    }
}
