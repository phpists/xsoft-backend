<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Staff\DeleteStaffRequest;
use App\Http\Requests\Staff\SaveStaffRequest;
use App\Http\Requests\Staff\UpdateStaffRequest;
use App\Http\Resources\Staff\StaffAllResource;
use App\Http\Resources\Staff\StaffResource;
use App\Http\Resources\User\UserResource;
use App\Http\Services\FileService;
use App\Models\Media;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class StaffController extends CoreController
{
    public static function routers()
    {
        Route::group(
            [
                'prefix' => 'staff',
                'middleware' => 'auth:api'
            ],
            function () {
                Route::get('get-all-staff', [static::class, 'getAllStaff']);
                Route::get('get-staff', [static::class, 'getStaff']);
                Route::post('add-staff', [static::class, 'addStaff']);
                Route::post('edit-staff', [static::class, 'editStaff']);
                Route::delete('delete-staff', [static::class, 'deleteStaff']);

                Route::get('generate-password', [static::class, 'generatePassword']);
            }
        );
    }

    public function getAllStaff(Request $request)
    {
        $data = $request->all();
        $auth = User::find(auth()->id());

        $builder = User::query();
        $builder->where('company_id', $auth->getCurrentCompanyId());
        $builder->whereIn('role_id', [User::STAFF, User::CUSTOMER]);

        if (isset($data['q'])) {
            $query = $data['q'];
            $builder->where('first_name', 'like', "%$query%")
                ->orWhere('last_name', 'like', "%$query%")
                ->orWhere('email', 'like', "%$query%");
        }

        $this->setSorting($builder, [
            'id' => 'id',
        ]);
        $staffs = $builder->paginate($this->getPerPage($data['perPage'] ?? 15));

        return $this->responseSuccess(new StaffAllResource($staffs, false));
    }

    public function getStaff(Request $request)
    {
        $data = $request->all();
        $auth = User::find(auth()->id());
        $staff = User::where('company_id', $auth->getCurrentCompanyId())
            ->where('id', $data['id'])
            ->first();

        if (empty($staff)){
            return $this->responseError('Співробітника з таким id не знайдено');
        }

        return $this->responseSuccess([
           'staff' => new StaffResource($staff)
        ]);
    }

    public function addStaff(SaveStaffRequest $request)
    {
        $data = $request->all();
        $auth = User::find(auth()->id());
        $staff = User::create([
            'company_id' => $auth->getCurrentCompanyId(),
            'role_id' => $data['role_id'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'color' => $data['color'],
            'email' => $data['email'],
            'comment' => $data['comment'],
            'password' => $data['password'],
        ]);

        if ($staff) {
            if ($request->hasFile('media')) {
                foreach ($data['media'] as $media) {
                    Media::create([
                        'type_id' => Media::STAFF_MEDIA,
                        'parent_id' => $staff->id,
                        'file' => FileService::saveFile('uploads', "media", $media),
                    ]);
                }
            }
        }

        return $this->responseSuccess([
            'message' => 'Працівник успішно збережений',
            'staff' => new StaffResource($staff),
        ]);
    }

    public function editStaff(UpdateStaffRequest $request)
    {
        $data = $request->all();
        $auth = User::find(auth()->id());
        $staff = User::find($data['id']);
        $staff->update([
            'company_id' => $auth->getCurrentCompanyId(),
            'role_id' => $data['role_id'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'color' => $data['color'],
            'comment' => $data['comment'],
            'password' => $data['password'],
        ]);

        if ($staff) {
            if ($request->hasFile('media')) {
                foreach ($data['media'] as $media) {
                    Media::create([
                        'type_id' => Media::STAFF_MEDIA,
                        'parent_id' => $staff->id,
                        'file' => FileService::saveFile('uploads', "media", $media),
                    ]);
                }
            }
        }

        return $this->responseSuccess([
            'message' => 'Працівник успішно відредагований',
            'staff' => new StaffResource($staff),
        ]);
    }

    public function deleteStaff(DeleteStaffRequest $request)
    {
        $data = $request->all();
        $auth = User::find(auth()->id());
        User::where('company_id', $auth->getCurrentCompanyId())
            ->whereIn('id', $data['idx'])
            ->delete();

        return $this->responseSuccess([
            'message' => 'Працівник успішно видалені',
        ]);
    }

    public function generatePassword(Request $request)
    {
        return $this->responseSuccess([
           'password' => Str::random(5)
        ]);
    }
}



