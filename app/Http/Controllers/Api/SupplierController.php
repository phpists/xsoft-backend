<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Supplier\SupplierResource;
use App\Http\Resources\Supplier\SuppliersResource;
use App\Http\Resources\Traits\HasFullInfoFlag;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
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
                Route::post('add-supplier', [static::class, 'addSupplier']);
            }
        );
    }

    public function getSuppliers(Request $request)
    {
        $data = $request->all();
        $auth = User::find(auth()->id());

        $builder = User::query();
        $builder->where('company_id', $auth->getCurrentCompanyId());
        $builder->where('parent_id', auth()->id());
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

    public function addSupplier(Request $request)
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
}
