<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Warehouse\DeleteWarehouseRequest;
use App\Http\Requests\Warehouse\GetWarehouseRequest;
use App\Http\Requests\Warehouse\SaveWarehouseRequest;
use App\Http\Requests\Warehouse\UpdateWarehouseRequest;
use App\Http\Resources\Warehouse\WarehouseResource;
use App\Http\Resources\Warehouse\WarehousesResource;
use App\Models\Brand;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Route;

class WarehouseController extends CoreController
{
    public static function routers()
    {
        Route::group(
            [
                'prefix' => 'warehouse',
                'middleware' => 'auth:api'
            ],
            function () {
                Route::get('get-all-warehouses', [static::class, 'getWarehouses']);
                Route::get('get-warehouse', [static::class, 'getWarehouse']);

                Route::post('add-warehouse', [static::class, 'addWarehouse']);
                Route::post('update-warehouse', [static::class, 'updateWarehouse']);
                Route::delete('delete-warehouse', [static::class, 'deleteWarehouse']);
            }
        );
    }

    public function getWarehouses(Request $request)
    {
        $data = $request->all();
        $auth = User::find(auth()->id());
        $builder = Warehouse::query();
        $builder->where('company_id', $auth->getCurrentCompanyId());

        if (isset($data['q'])) {
            $query = $data['q'];
            $builder->where('title', 'like', "%$query%");
        }

        $this->setSorting($builder, [
            'id' => 'id',
        ]);

        $warehouses = $builder->get();

        return $this->responseSuccess([
            'warehouses' => new WarehousesResource($warehouses)
        ]);
    }

    public function getWarehouse(GetWarehouseRequest $request)
    {
        $data = $request->all();
        $warehouse = Warehouse::find($data['id']);

        return $this->responseSuccess([
            'warehouse' => new WarehouseResource($warehouse)
        ]);
    }

    public function addWarehouse(SaveWarehouseRequest $request)
    {
        $data = $request->all();
        $auth = User::find(auth()->id());
        $warehouse = Warehouse::create([
            'company_id' => $auth->getCurrentCompanyId(),
            'title' => $data['title'],
            'description' => isset($data['description']) ? $data['description'] : null,
        ]);

        return $this->responseSuccess([
            'message' => 'Склад успішно збережений',
            'warehouse' => new WarehouseResource($warehouse),
        ]);
    }

    public function updateWarehouse(UpdateWarehouseRequest $request)
    {
        $data = $request->all();
        $auth = User::find(auth()->id());
        $warehouse = Warehouse::find($data['id']);
        $warehouse->update([
            'company_id' => $auth->getCurrentCompanyId(),
            'title' => $data['title'],
            'description' => isset($data['description']) ? $data['description'] : null,
        ]);

        return $this->responseSuccess([
            'message' => 'Склад успішно відредагований',
            'warehouse' => new WarehouseResource($warehouse),
        ]);
    }

    public function deleteWarehouse(DeleteWarehouseRequest $request)
    {
        $data = $request->all();
        Warehouse::whereIn('id', $data['idx'])->delete();

        return $this->responseSuccess([
            'message' => 'Склад успішно видалений',
        ]);
    }
}
