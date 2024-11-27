<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * User
     */
    const SUPER_ADMIN = 1;
    const ADMIN = 3;
    const MANAGER = 5;
    const CUSTOMER = 2;
    const STAFF = 4;
    const SUPPLIERS = 6;

    protected $fillable = [
        'parent_id',
        'company_id',
        'role_id',
        'category_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'password',
        'phones',
        'color',
        'bd_date',
        'comment',
        'tags'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function company()
    {
        return $this->hasOne(Company::class, 'id', 'company_id');
    }

    public function media()
    {
        return $this->hasMany(Media::class, 'parent_id', 'id')
            ->where('type_id', Media::USER_MEDIA);
    }

    public function staffMedia()
    {
        return $this->hasMany(Media::class, 'parent_id', 'id')
            ->where('type_id', Media::STAFF_MEDIA);
    }

    public function userBranch()
    {
        return $this->hasMany(UserBranch::class, 'user_id', 'id');
    }

    public static function setUserBd($params)
    {
        if ($params['bd_day']) {
            return date('Y-m-d', strtotime($params['bd_day']));
        } else {
            return null;
        }
    }

    public function getCurrentCompanyId()
    {
        return $this->company_id;
    }

    public function isSuperAdmin()
    {
        return $this->role_id == User::SUPER_ADMIN ? true : false;
    }

    public function getUserBranch()
    {
        return CompanyBranches::select('companies_branches.*')
            ->leftJoin('users_branches', 'users_branches.branch_id', 'companies_branches.id')
            ->where('users_branches.user_id', $this->id)
            ->get();
    }

    public function getFullName()
    {
        return $this->first_name . ' ' . $this->last_name;
    }
}
