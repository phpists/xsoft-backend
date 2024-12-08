<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Supplier\DeleteSupplierRequest;
use App\Http\Requests\Supplier\StoreSupplierRequest;
use App\Http\Requests\Supplier\UpdateSupplierRequest;
use App\Http\Resources\Supplier\SupplierResource;
use App\Http\Resources\Supplier\SuppliersResource;
use App\Http\Services\FileService;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

class SupplierController extends CoreController
{
    public static function routers()
    {
        Route::group(
            [
                'prefix' => 'supplier',
                'middleware' => 'auth:api'
            ],
            function () {
                Route::get('get-suppliers', [static::class, 'getSuppliers']);
                Route::get('get-supplier', [static::class, 'getSupplier']);
                Route::post('add-supplier', [static::class, 'addSupplier']);
                Route::post('edit-supplier', [static::class, 'editSupplier']);
                Route::delete('delete-supplier', [static::class, 'deleteSupplier']);
            }
        );
    }

    public function getSuppliers(Request $request)
    {
        $data = $request->all();
        $auth = User::find(auth()->id());

        $builder = User::query();
        $builder->where('company_id', $auth->getCurrentCompanyId());
        $builder->where('role_id', User::SUPPLIERS);

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

        return $this->responseSuccess(new SuppliersResource($clients, false));
    }

    public function getSupplier(Request $request)
    {
        $data = $request->all();
        $supplier = User::where('id', $data['id'])
            ->where('role_id', User::SUPPLIERS)
            ->first();

        if (empty($supplier)) {
            return $this->responseError('Постачальник з таким id не знайдено');
        }

        return $this->responseSuccess([
            'supplier' => new SupplierResource($supplier)
        ]);
    }

    public function addSupplier(StoreSupplierRequest $request)
    {
        $data = $request->all();
        $auth = User::find(auth()->id());
        $supplier = User::create([
            'company_id' => $auth->getCurrentCompanyId(),
            'parent_id' => auth()->id(),
            'role_id' => User::SUPPLIERS,
            "category_id" => $data['category_id'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'color' => $data['color'],
            'comment' => $data['comment'],
            'phones' => json_encode($data['phones']),
            'email' => $data['email'],
            'password' => Hash::make(rand(1, 1000)),
        ]);

        return $this->responseSuccess([
            'message' => 'Постачальник успішно збережений',
            'user' => new SupplierResource($supplier),
        ]);
    }

    public function editSupplier(UpdateSupplierRequest $request)
    {
        $data = $request->all();
        $auth = User::find(auth()->id());
        $supplier = User::find($data['id']);
        $supplier->update([
            'company_id' => $auth->getCurrentCompanyId(),
            'parent_id' => auth()->id(),
            'role_id' => User::SUPPLIERS,
            "category_id" => $data['category_id'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'color' => $data['color'],
            'comment' => $data['comment'],
            'phones' => json_encode($data['phones']),
            'password' => Hash::make(rand(1, 1000)),
        ]);

        return $this->responseSuccess([
            'message' => 'Постачальник успішно відредагований',
            'user' => new SupplierResource($supplier),
        ]);
    }

    public function deleteSupplier(DeleteSupplierRequest $request)
    {
        $data = $request->all();
        User::where('parent_id', auth()->id())
            ->whereIn('id', $data['idx'])
            ->delete();

        return $this->responseSuccess([
            'message' => 'Постачальник успішно видалені',
        ]);
    }
}
