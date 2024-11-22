<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyBranches extends Model
{
    /**
     * Філії компаній
     */

    use HasFactory;

    protected $table = 'companies_branches';
    protected $fillable = [
        'company_id',
        'title',
        'location',
        'phones'
    ];
}
