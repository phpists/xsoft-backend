<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductMovement\SaveProductMovementRequest;
use App\Http\Resources\ProductsMovement\ProductsMovementResource;
use App\Http\Resources\ProductsMovement\ProductsMovementsItemsResource;
use App\Http\Resources\ProductsMovement\ProductsMovementsResource;
use App\Http\Resources\Supplier\SuppliersCollectResource;
use App\Models\Measurement;
use App\Models\ProductMovement;
use App\Models\ProductsMovementItem;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

class ProductMovementController extends CoreController
{
    public static function routers()
    {
        Route::group(
            [
                'prefix' => 'product-movement',
                'middleware' => 'auth:api'
            ],
            function () {
                Route::get('get-product-movement-info', [static::class, 'getProductMovementInfo']);
                Route::get('get-products-movement', [static::class, 'getProductsMovement']);
                Route::post('add-product-movement', [static::class, 'addProductMovement']);
            }
        );
    }

    public function getProductMovementInfo(Request $request)
    {
        try {
            DB::beginTransaction();

            $auth = User::find(auth()->id());
            $company_id = $auth->getCurrentCompanyId();

            $builder = User::query();
            $builder->whereHas('userCompanies', function ($query) use ($auth, $company_id) {
                return $query->where('company_id', $company_id);
            });
            $builder->whereIn('role_id', [User::MANAGER, User::STAFF, User::ADMIN]);

            $staffs = $builder->get();
            $warehouses = Warehouse::all();
            $measurements = Measurement::all();
            $suppliers = User::where('role_id', User::SUPPLIERS)
                ->where('company_id', $company_id)
                ->get();

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            return back()->withErrors(["Помилка: {$exception->getMessage()}"]);
        }

        return $this->responseSuccess([
            'company_id' => $company_id,
            'staffs' => $staffs,
            'warehouses' => $warehouses,
            'measurements' => $measurements,
            'suppliers' => new SuppliersCollectResource($suppliers),
        ]);
    }

    public function getProductsMovement(Request $request)
    {
        $data = $request->all();
        $auth = User::find(auth()->id());

        $builder = ProductMovement::query();
        $builder->where('company_id', $auth->getCurrentCompanyId());

        $this->setSorting($builder, [
            'id' => 'id',
        ]);

        $productMovements = $builder->paginate($this->getPerPage($data['perPage'] ?? 15));

        return $this->responseSuccess([
            'products_movement' => new ProductsMovementsResource($productMovements)
        ]);
    }

    public function addProductMovement(SaveProductMovementRequest $request)
    {
        $data = $request->all();
        $auth = User::find(auth()->id());
        $productMovement = ProductMovement::create([
            'staff_id' => $data['staff_id'],
            'company_id' => $auth->getCurrentCompanyId(),
            'warehouse_id' => $data['warehouse_id'],
            'supplier_id' => $data['supplier_id'],
            'type_id' => ProductMovement::PARISH,
            'total_price' => $data['total_price'],
            'date_create' => $data['date_create'],
            'time_create' => $data['time_create'],
            'debt' => isset($data['debt']) ? $data['debt'] : null,
            'installment_payment' => isset($data['installment_payment']) ? $data['installment_payment'] : null,
            'box_office_date' => $data['box_office_date']
        ]);

        if ($productMovement) {
            if (isset($data['items'])) {
                foreach ($data['items'] as $item) {
                    ProductsMovementItem::create([
                        'product_movement_id' => $productMovement->id,
                        'product_id' => $item['product_id'],
                        'type_id' => ProductMovement::PARISH,
                        'qty' => $item['qty'],
                        'measurement_id' => $item['measurement_id'],
                        'cost_price' => $item['cost_price'],
                        'retail_price' => $item['retail_price']
                    ]);
                }
            }
        }

        return $this->responseSuccess([
            'message' => 'Прихід успішно збережений',
            'product_movement' => new ProductsMovementResource($productMovement)
        ]);
    }
}
