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
use App\Models\UserCompany;
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
        $builder->whereIn('role_id', [User::MANAGER, User::STAFF, User::ADMIN]);


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
        $companyId = $auth->getCurrentCompanyId();

        // Створюємо нового працівника
        $staff = $this->createStaff($auth, $data, $companyId, $password);

        // Відправляємо електронний лист
        $this->sendWelcomeEmail($staff, $password);

        // Прив'язка до компанії
        UserCompany::assignToCompany($staff->id, $companyId, UserCompany::DESIGNATED_COMPANY);

        // Додаємо медіафайли
        if ($request->hasFile('media')) {
            $this->handleMedia($staff, $data['media']);
        }

        // Прив'язка до відділень
        if (isset($data['branches'])) {
            $this->assignToBranches($staff, $data['branches']);
        }

        return $this->responseSuccess([
            'message' => 'Працівник успішно збережений',
            'staff' => new StaffResource($staff),
        ]);
    }

    private function createStaff($auth, $data, $companyId, $password)
    {
        return User::create([
            'parent_id' => $auth->id,
            'company_id' => $companyId,
            'role_id' => $data['role_id'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'color' => $data['color'],
            'email' => $data['email'],
            'comment' => $data['comment'],
            'position_id' => $data['position_id'],
            'department_id' => $data['department_id'],
            'password' => Hash::make($password),
            'phones' => json_encode($data['phones']),
        ]);
    }

    private function sendWelcomeEmail($staff, $password)
    {
        Mail::to($staff->email)->send(new StoreStaffMail($staff, $password));
    }


    private function handleMedia($staff, $mediaFiles)
    {
        foreach ($mediaFiles as $media) {
            Media::create([
                'type_id' => Media::STAFF_MEDIA,
                'parent_id' => $staff->id,
                'file' => FileService::saveFile('uploads', "media", $media),
            ]);
        }
    }

    private function assignToBranches($staff, $branches)
    {
        foreach ($branches as $branchId) {
            UserBranch::updateOrCreate(
                [
                    'user_id' => $staff->id,
                    'branch_id' => $branchId,
                ],
                [
                    'user_id' => $staff->id,
                    'branch_id' => $branchId,
                ]
            );
        }
    }


    public function editStaff(UpdateStaffRequest $request)
    {
        $data = $request->all();
        $auth = User::find(auth()->id());
        $staff = User::find($data['id']);

        // Оновлення даних працівника
        $this->updateStaffDetails($staff, $auth, $data);

        // Додавання медіафайлів
        if ($request->hasFile('media')) {
            $this->handleMedia($staff, $data['media']);
        }

        // Оновлення прив'язки до відділень
        if (isset($data['branches'])) {
            $this->updateBranches($auth, $data['branches']);
        }

        return $this->responseSuccess([
            'message' => 'Працівник успішно відредагований',
            'staff' => new StaffResource($staff),
        ]);
    }

    private function updateStaffDetails($staff, $auth, $data)
    {
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
    }


    private function updateBranches($auth, $branches)
    {
        UserBranch::where('user_id', $auth->id)->delete();

        foreach ($branches as $branchId) {
            UserBranch::updateOrCreate(
                [
                    'user_id' => $auth->id,
                    'branch_id' => $branchId,
                ],
                [
                    'user_id' => $auth->id,
                    'branch_id' => $branchId,
                ]
            );
        }
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



