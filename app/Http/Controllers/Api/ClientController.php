<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Resources\User\UserResource;
use App\Http\Resources\User\UsersResource;
use App\Http\Services\FileService;
use App\Models\Media;
use App\Models\User;
use App\Models\UserCategory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

class ClientController extends CoreController
{
    public static function routers()
    {
        Route::group(
            [
                'prefix' => 'client',
                'middleware' => 'auth:api'
            ],
            function () {
                Route::get('get-clients', [static::class, 'getClients']);
                Route::post('add-client', [static::class, 'addClient']);
                Route::post('edit-client', [static::class, 'editClient']);
                Route::get('get-client', [static::class, 'getClient']);


                Route::get('get-users-categories', [static::class, 'getUsersCategories']);
            }
        );
    }

    public function getClients(Request $request)
    {
        $data = $request->all();
        $builder = User::query();
        $this->setSorting($builder, [
            'id' => 'id',
        ]);
        $clients = $builder->paginate($this->getPerPage($data['perPage'] ?? 15));

        return $this->responseSuccess(new UsersResource($clients, false));
    }

    public function addClient(StoreUserRequest $request)
    {
        $data = $request->all();
        $user = User::create([
            'role_id' => User::CUSTOMER,
            "category_id" => $data['category_id'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'phone' => $data['phone'],
            'color' => $data['color'],
            'bd_date' => User::setUserBd($data),
            'comment' => $data['comment'],
            'phones' => json_encode($data['phones']),
            'email' => $data['email'],
            'password' => Hash::make(rand(1, 1000)),
        ]);

        if ($user) {
            if ($request->hasFile('media')) {
                foreach ($data['media'] as $media) {
                    Media::create([
                        'type_id' => Media::USER_MEDIA,
                        'parent_id' => $user->id,
                        'file' => FileService::saveFile('uploads', "media", $media),
                    ]);
                }
            }
        }

        return $this->responseSuccess([
            'message' => 'Клієнт успішно збережений',
            'user' => new UserResource($user),
        ]);
    }

    public function editClient(Request $request)
    {
        $data = $request->all();

        $user = User::where('id', $data['id'])->update([
            'role_id' => User::CUSTOMER,
            "category_id" => $data['category_id'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'phone' => $data['phone'],
            'color' => $data['color'],
            'bd_date' => User::setUserBd($data),
            'comment' => $data['comment'],
            'phones' => json_encode($data['phones']),
            'email' => $data['email'],
        ]);

        if ($user) {
            if ($request->hasFile('media')) {
                foreach ($data['media'] as $media) {
                    Media::create([
                        'type_id' => Media::USER_MEDIA,
                        'parent_id' => $user->id,
                        'file' => FileService::saveFile('uploads', "media", $media),
                    ]);
                }
            }
        }

        return $this->responseSuccess([
            'message' => 'Клієнт успішно відредагований',
            'user' => new UserResource($user),
        ]);
    }

    public function getClient(Request $request)
    {
        $data = $request->all();
        $user = User::find($data['id']);

        return $this->responseSuccess([
            'user' => new UserResource($user),
        ]);
    }

    public function getUsersCategories(Request $request)
    {
        return $this->responseSuccess([
            'categories' => UserCategory::all()
        ]);
    }
}
