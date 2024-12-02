<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Media\DeleteMediaRequest;
use App\Http\Requests\User\DeleteUserRequest;
use App\Http\Requests\User\SaveClientMediaRequest;
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
                Route::delete('delete-client', [static::class, 'deleteClient']);

                Route::get('get-users-categories', [static::class, 'getUsersCategories']);

                Route::post('save-client-media', [static::class, 'saveClientMedia']);
                Route::delete('delete-client-media', [static::class, 'deleteClientMedia']);
            }
        );
    }

    public function getClients(Request $request)
    {
        $data = $request->all();
        $auth = User::find(auth()->id());

        $builder = User::query();
        $builder->where('company_id', $auth->getCurrentCompanyId());
        $builder->where('role_id', User::CUSTOMER);

        if (isset($data['q'])) {
            $query = $data['q'];
            $builder->where('first_name', 'like', "%$query%")
                ->orWhere('last_name', 'like', "%$query%")
                ->orWhere('email', 'like', "%$query%");
        }

        $this->setSorting($builder, [
            'id' => 'id',
        ]);
        $clients = $builder->paginate($this->getPerPage($data['perPage'] ?? 15));

        return $this->responseSuccess(new UsersResource($clients, false));
    }

    public function addClient(StoreUserRequest $request)
    {
        $data = $request->all();
        $auth = User::find(auth()->id());
        $rand = rand(100000, 9999999);
        $user = User::create([
            'company_id' => $auth->getCurrentCompanyId(),
            'parent_id' => auth()->id(),
            'role_id' => User::CUSTOMER,
            "category_id" => $data['category_id'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'phone' => isset($data['phone']) ? $data['phone'] : null,
            'color' => isset($data['color']) ? $data['color'] : null,
            'bd_date' => isset($params['bd_day']) ? User::setUserBd($data) : null,
            'comment' => isset($data['comment']) ? $data['comment'] : null,
            'phones' => json_encode($data['phones']),
            'tags' => isset($data['tags']) ? json_encode($data['tags']) : null,
            'email' => $rand . 'user@gmail.com',
            'password' => Hash::make($rand),
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

        $user = User::where('id', $data['id'])->first();
        $user->update([
            'role_id' => User::CUSTOMER,
            "category_id" => $data['category_id'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'phone' => isset($data['phone']) ? $data['phone'] : null,
            'color' => isset($data['color']) ? $data['color'] : null,
            'bd_date' => isset($params['bd_day']) ? User::setUserBd($data) : null,
            'comment' => isset($data['comment']) ? $data['comment'] : null,
            'phones' => json_encode($data['phones']),
            'tags' => json_encode($data['tags']),
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

    public function deleteClient(Request $request)
    {
        $data = $request->all();
        User::where('parent_id', auth()->id())
            ->whereIn('id', $data['idx'])
            ->delete();

        return $this->responseSuccess([
            'message' => 'Клієнти успішно видалені',
        ]);
    }

    public function getUsersCategories(DeleteUserRequest $request)
    {
        return $this->responseSuccess([
            'categories' => UserCategory::all()
        ]);
    }

    public function saveClientMedia(SaveClientMediaRequest $request)
    {
        $data = $request->all();
        $user = User::find($data['user_id']);

        if (empty($user)) {
            return $this->responseError([
                'message' => 'Клієнт не знайдений'
            ]);
        }

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
            'user' => new UserResource($user),
        ]);
    }

    public function deleteClientMedia(DeleteMediaRequest $request)
    {
        $data = $request->all();
        $media = Media::where('id', $data['id'])->first();

        if ($media) {
            FileService::removeFile('uploads', 'media', $media->file);

            $media->delete();
        }

        return $this->responseSuccess([
            'message' => 'Медіа успішно видалений'
        ]);
    }
}
