<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCompany extends Model
{
    use HasFactory;

    /**
     * Type id
     */
    const MY_COMPANY = 1; // Моя компанія
    const DESIGNATED_COMPANY = 2; // Назначена компанія

    protected $table = 'users_companies';
    protected $fillable = [
        'user_id',
        'company_id',
        'type_id',
        'is_parent',
    ];

    /**
     * Привязка користувача до компанії
     * @param $userId
     * @param $companyId
     * @param $type
     * @param $isParent
     */
    public static function assignToCompany($userId, $companyId, $type, $isParent = false)
    {
        UserCompany::updateOrCreate(
            [
                'user_id' => $userId,
                'company_id' => $companyId,
            ],
            [
                'user_id' => $userId,
                'company_id' => $companyId,
                'type_id' => $type,
                'is_parent' => $isParent
            ]
        );
    }
}

