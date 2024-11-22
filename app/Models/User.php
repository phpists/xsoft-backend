<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
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
    const ADMIN = 1;
    const CUSTOMER = 2;
    const MIDDLE_ADMIN = 3;
    const STAFF = 4;

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
        return $this->company->id;
    }
}
