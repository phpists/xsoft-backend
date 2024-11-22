<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class RoleController extends CoreController
{
    public static function routers()
    {
        Route::group(
            [
                'prefix' => 'role',
                'middleware' => 'auth:api'
            ],
            function () {
                Route::get('get-roles', [static::class, 'getRoles']);
            }
        );
    }

    public function getRoles(Request $request)
    {
        $roles = Role::whereNotIn('id', [
            User::ADMIN, User::MIDDLE_ADMIN
        ])->get();

        return $this->responseSuccess([
            'roles' => $roles,
        ]);
    }
}
