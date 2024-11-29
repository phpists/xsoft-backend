<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\ResetPasswordUserRequest;
use App\Http\Resources\User\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

class ProfileController extends CoreController
{
    public static function routers()
    {
        Route::group(
            [
                'prefix' => 'profile',
            ],
            function () {
                Route::post('update-user', [static::class, 'updateUser']);
                Route::post('update-user-password', [static::class, 'updateUserPassword']);
            }
        );
    }

    /**
     * Оновлення даних користувача
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateUser(Request $request)
    {
        $data = $request->all();
        $user = User::where('id', $data['user_id'])->first();

        if (empty($user)) {
            return $this->responseError('Користувач не знайдений');
        } else {
            $user->update([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'phone' => $data['phone'],
                'color' => $data['color']
            ]);
        }

        return $this->responseSuccess([
            'message' => 'Дані користувача успішно відредаговані',
            'user' => new UserResource($user),
        ]);
    }

    /**
     * Оновлення паролю користувача
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateUserPassword(ResetPasswordUserRequest $request)
    {
        $data = $request->all();
        $user = User::where('id', $data['user_id'])->first();

        if ($user) {
            $user->password = Hash::make($data['password']);
            if ($user->update()) {
                return $this->responseSuccess([
                    'message' => 'Пароль успішно оновлений',
                ], 200);
            }
        } else {
            return $this->responseSuccess([
                'message' => 'Такого користувача не існує',
            ], 429);
        }
    }
}
