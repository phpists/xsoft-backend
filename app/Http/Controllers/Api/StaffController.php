<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Staff\DeleteStaffMediaRequest;
use App\Http\Requests\Staff\DeleteStaffRequest;
use App\Http\Requests\Staff\SaveStaffMediaRequest;
use App\Http\Requests\Staff\SaveStaffRequest;
use App\Http\Requests\Staff\UpdateStaffRequest;
use App\Http\Resources\Staff\StaffAllResource;
use App\Http\Resources\Staff\StaffResource;
use App\Http\Services\FileService;
use App\Mail\StoreStaffMail;
use App\Models\Department;
use App\Models\Media;
use App\Models\Position;
use App\Models\User;
use App\Models\UserBranch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
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

                Route::get('get-staff-info', [static::class, 'getStaffInfo']);
                Route::get('generate-password', [static::class, 'generatePassword']);

                Route::post('save-staff-media', [static::class, 'saveStaffMedia']);
                Route::delete('delete-staff-media', [static::class, 'deleteStaffMedia']);
            }
        );
    }

    public function getAllStaff(Request $request)
    {
        $data = $request->all();
        $auth = User::find(auth()->id());

        $builder = User::query();
        $builder->where('company_id', $auth->getCurrentCompanyId());
        $builder->where('parent_id', auth()->id());

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

        if (empty($staff)) {
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
        $password = $data['password'];
        $staff = User::create([
            'parent_id' => $auth->id,
            'company_id' => $auth->getCurrentCompanyId(),
            'role_id' => $data['role_id'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'color' => $data['color'],
            'email' => $data['email'],
            'comment' => $data['comment'],
            'position_id' => $data['position_id'],
            'department_id' => $data['department_id'],
            'password' => Hash::make($data['password']),
            'phones' => json_encode($data['phones']),
        ]);

        if ($staff) {
            Mail::to($data['email'])->send(new StoreStaffMail($staff, $password));

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

        if (isset($data['branches'])){
            foreach ($data['branches'] as $branchId){
                UserBranch::updateOrCreate([
                    'user_id' => $staff->id,
                    'branch_id' => $branchId
                ], [
                    'user_id' => $staff->id,
                    'branch_id' => $branchId
                ]);
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

        $params = [
            'company_id' => $auth->getCurrentCompanyId(),
            'role_id' => $data['role_id'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'color' => $data['color'],
            'comment' => $data['comment'],
            'position_id' => $data['position_id'],
            'department_id' => $data['department_id'],
        ];

        if (isset($data['phones'])) {
            $params['phones'] = json_encode($data['phones']);
        }

        if (isset($data['password'])) {
            $params['password'] = Hash::make($data['password']);
        }

        $staff->update($params);

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

        if (isset($data['branches'])){
            UserBranch::where('user_id', $auth->id)->delete();
            foreach ($data['branches'] as $branchId){
                UserBranch::updateOrCreate([
                    'user_id' => $auth->id,
                    'branch_id' => $branchId
                ], [
                    'user_id' => $auth->id,
                    'branch_id' => $branchId
                ]);
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

    public function getStaffInfo()
    {
        try {
            DB::beginTransaction();

            $positions = Position::all();
            $departments = Department::all();

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            return back()->withErrors(["Помилка: {$exception->getMessage()}"]);
        }

        return $this->responseSuccess([
            'positions' => $positions,
            'departments' => $departments
        ]);
    }

    public function generatePassword(Request $request)
    {
        return $this->responseSuccess([
            'password' => Str::random(5)
        ]);
    }

    public function saveStaffMedia(SaveStaffMediaRequest $request)
    {
        $data = $request->all();
        $staff = User::where('id', $data['staff_id'])->first();

        if (empty($staff)) {
            return $this->responseError('Працівник не знайдений');
        }

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
            'staff' => new StaffResource($staff),
        ]);
    }

    public function deleteStaffMedia(DeleteStaffMediaRequest $request)
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



