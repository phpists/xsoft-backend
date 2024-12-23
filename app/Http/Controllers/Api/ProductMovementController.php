<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductMovement\AddProductMovementSaleRequest;
use App\Http\Requests\ProductMovement\DeleteProductMovementRequest;
use App\Http\Requests\ProductMovement\GetProductMovementItemRequest;
use App\Http\Requests\ProductMovement\GetProductMovementRequest;
use App\Http\Requests\ProductMovement\SaveProductMovementRequest;
use App\Http\Requests\ProductMovement\UpdateProductMovementRequest;
use App\Http\Resources\Product\ProductMovementResource;
use App\Http\Resources\ProductsMovement\ProductsMovementResource;
use App\Http\Resources\ProductsMovement\ProductsMovementsItemResource;
use App\Http\Resources\ProductsMovement\ProductsMovementsItemsResource;
use App\Http\Resources\ProductsMovement\ProductsMovementsResource;
use App\Http\Resources\Supplier\SuppliersCollectResource;
use App\Http\Resources\Traits\HasFullInfoFlag;
use App\Models\CashesHistory;
use App\Models\Measurement;
use App\Models\Product;
use App\Models\ProductMovement;
use App\Models\ProductsMovementItem;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Database\Query\Builder;
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
                Route::get('get-product-movement', [static::class, 'getProductMovement']);
                Route::get('get-product-movement-item', [static::class, 'getProductMovementItem']);
                Route::get('search-products-movement', [static::class, 'searchProductsMovement']);

                Route::post('add-product-movement', [static::class, 'addProductMovement']);
                Route::post('update-product-movement', [static::class, 'updateProductMovement']);
                Route::delete('delete-products-movement', [static::class, 'deleteProductMovement']);

                Route::post('add-product-movement-sale', [static::class, 'addProductMovementSale']);

                Route::get('get-product-by-id', [static::class, 'getProductById']);
            }
        );
    }

    public function getProductMovementInfo(Request $request)
    {
        try {
            DB::beginTransaction();

            $auth = User::find(auth()->id());
            $company_id = $auth->getCurrentCompanyId();

            $staffs = User::with('userCompanies')
                ->whereIn('role_id', [User::MANAGER, User::STAFF, User::ADMIN])
                ->whereHas('userCompanies', function ($query) use ($company_id) {
                    $query->where('company_id', $company_id);
                })->get();

            $warehouses = Warehouse::where('company_id', $company_id)->get();

            $suppliers = User::where('role_id', User::SUPPLIERS)
                ->where('company_id', $company_id)
                ->get();

            $measurements = Measurement::all();
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
            'total_price' => 'total_price'
        ]);
        $builder->orderBy('id', 'desc');
        $productMovements = $builder->paginate($this->getPerPage($data['perPage'] ?? 15));

        return $this->responseSuccess([
            'products_movement' => new ProductsMovementsResource($productMovements)
        ]);
    }

    public function getProductMovement(GetProductMovementRequest $request)
    {
        $data = $request->all();
        $productMovement = ProductMovement::where('id', $data['product_movement_id'])->first();

        if (empty($productMovement)) {
            return $this->responseError('Прихід товару не знайдений');
        }

        return $this->responseSuccess([
            'product_movement' => new ProductMovementResource($productMovement)
        ]);
    }

    public function getProductMovementItem(GetProductMovementItemRequest $request)
    {
        $data = $request->all();
        $productMovementItem = ProductsMovementItem::where('id', $data['product_movement_item_id'])->first();

        if (empty($productMovementItem)) {
            return $this->responseError('Одиниця прихід товару не знайдена');
        }

        return $this->responseSuccess([
            'item' => new ProductsMovementsItemResource($productMovementItem)
        ]);
    }

    /**
     * Прихід товару
     * @param SaveProductMovementRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
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
            CashesHistory::create([
                'user_id' => $auth->id,
                'cashes_id' => $data['cashes']['cashes_id'],
                'type_id' => ProductMovement::PARISH,
                'amount' => $data['cashes']['amount'],
                'amount_cashes' => 0
            ]);

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

    /**
     * Редагування приходу товарів
     * @param UpdateProductMovementRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateProductMovement(UpdateProductMovementRequest $request)
    {
        $data = $request->all();
        $auth = User::find(auth()->id());
        $productMovement = ProductMovement::find($data['id']);
        $productMovement->update([
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


        if (isset($data['items'])) {
            foreach ($data['items'] as $item) {
                ProductsMovementItem::updateOrCreate([
                    'product_movement_id' => $productMovement->id,
                    'product_id' => $item['product_id'],
                ], [
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

        return $this->responseSuccess([
            'message' => 'Прихід успішно відредагований!',
            'product_movement' => new ProductsMovementResource($productMovement)
        ]);
    }

    public function deleteProductMovement(DeleteProductMovementRequest $request)
    {
        $data = $request->all();
        $productMovements = ProductMovement::whereIn('id', $data['idx'])->get();

        if ($productMovements) {
            foreach ($productMovements as $productMovement) {
                ProductsMovementItem::where('product_movement_id', $productMovement->id)->delete();
                $productMovement->delete();
            }
        }

        return $this->responseSuccess([
            'message' => 'Дані успішно видалені',
        ]);
    }

    /**
     * Пошук ProductsMovementItem
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchProductsMovement(Request $request)
    {
        $data = $request->all();
        $builder = ProductsMovementItem::query();
        $builder->where('type_id', ProductMovement::PARISH);
        $builder->whereHas('product', function ($query) use ($data) {
            $q = $data['q'];
            return $query->where('title', 'LIKE', "%$q%")
                ->orWhere('article', 'LIKE', "%$q%");
        });

        $items = $builder->get();

        if (empty($items)) {
            return $this->responseSuccess([
                'product_movement_item' => []
            ]);
        }

        return $this->responseSuccess([
            'product_movement_items' => new ProductsMovementsItemsResource($items)
        ]);
    }

    /**
     * Продаж товарів
     */
    public function addProductMovementSale(AddProductMovementSaleRequest $request)
    {
        $data = $request->all();
        $auth = User::find(auth()->id());
        $newProductMovement = ProductMovement::create([
            'staff_id' => $data['staff_id'],
            'company_id' => $auth->getCurrentCompanyId(),
            'warehouse_id' => $data['warehouse_id'],
            'type_id' => $data['type_id'],
            'total_price' => $data['total_price'],
            'date_create' => $data['date_create'],
            'time_create' => $data['time_create'],
            'debt' => isset($data['debt']) ? $data['debt'] : null,
            'installment_payment' => isset($data['installment_payment']) ? $data['installment_payment'] : null,
            'box_office_date' => $data['box_office_date']
        ]);

        if ($newProductMovement){
            CashesHistory::create([
                'user_id' => $auth->id,
                'cashes_id' => $data['cashes']['cashes_id'],
                'type_id' => $data['type_id'],
                'amount' => $data['cashes']['amount'],
                'amount_cashes' => 0
            ]);
        }

        if (isset($data['items'])) {
            foreach ($data['items'] as $item) {
                $productsMovementItem = ProductsMovementItem::create([
                    'product_movement_id' => $newProductMovement->id,
                    'product_id' => $item['product_id'],
                    'type_id' => $data['type_id'],
                    'qty' => $item['qty'],
                    'measurement_id' => $item['measurement_id'],
                    'cost_price' => $item['cost_price'],
                    'retail_price' => $item['retail_price'],
                ]);

                if ($productsMovementItem) {
                    $productMovement = ProductsMovementItem::where('product_movement_id', $item['product_movement_id'])
                        ->where('product_id', $item['product_id'])
                        ->where('type_id', ProductMovement::PARISH)
                        ->first();

                    if ($productMovement) {
                        $productMovement->update([
                            'qty' => $productMovement->qty - $item['qty'],
                        ]);
                    }
                }
            }
        }

        return $this->responseSuccess([
            'message' => 'Дані успішно збережено',
            'product_movement' => new ProductsMovementResource($newProductMovement)
        ]);
    }

    public function getProductById(Request $request)
    {
        $data = $request->all();
        $product = ProductsMovementItem::where('product_id', $data['id'])
            ->first();

        if (empty($product)) {
            return $this->responseError('Всі товари продані!');
        }

        return $this->responseSuccess([
            'product' => new ProductsMovementsItemResource($product)
        ]);
    }
}
