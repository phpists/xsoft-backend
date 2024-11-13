<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Product\ProductResource;
use App\Http\Resources\Product\ProductsResource;
use App\Http\Resources\Traits\HasFullInfoFlag;
use App\Http\Services\FileService;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Measurement;
use App\Models\Media;
use App\Models\Product;
use App\Models\ProductItem;
use App\Models\Taxes;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

class ProductController extends CoreController
{
    public static function routers()
    {
        Route::group(
            [
                'prefix' => 'product',
                'middleware' => 'auth:api'
            ],
            function () {
                Route::get('get-products', [static::class, 'getProducts']);
                Route::post('add-product', [static::class, 'addProduct']);
                Route::post('update-product', [static::class, 'updateProduct']);

                Route::get('get-product-info', [static::class, 'getProductInfo']);
            }
        );
    }

    public function getProducts(Request $request)
    {
        $data = $request->all();
        $builder = Product::query();
        $builder->where('user_id', auth()->id());
        $this->setSorting($builder, [
            'id' => 'id',
        ]);
        $products = $builder->paginate($this->getPerPage($data['perPage'] ?? 15));

        return $this->responseSuccess(new ProductsResource($products, false));
    }

    public function addProduct(Request $request)
    {
        $data = $request->all();
        $product = Product::create([
            'user_id' => auth()->id(),
            'brand_id' => $data['brand_id'],
            'category_id' => $data['category_id'],
            'article' => $data['article'],
            'title' => $data['title'],
            'description' => $data['description'],
            'product_measure_id' => $data['product_measure_id'],
            'color' => $data['color'],
            'balance' => $data['balance'],
            'materials_used_quantity' => $data['materials_used_quantity'],
            'materials_used_measure_id' => $data['materials_used_measure_id'],
        ]);

        if (isset($data['items'])) {
            foreach ($data['items'] as $item) {
                ProductItem::create([
                    'product_id' => $product->id,
                    'tax_id' => $item['tax_id'],
                    'cost_price' => $item['cost_price'],
                    'retail_price' => $item['retail_price'],
                ]);
            }
        }

        if ($request->hasFile('media')) {
            foreach ($data['media'] as $media) {
                Media::create([
                    'type_id' => Media::PRODUCT_MEDIA,
                    'parent_id' => $product->id,
                    'file' => FileService::saveFile('uploads', "products", $media),
                ]);
            }
        }

        return $this->responseSuccess([
            'message' => 'Товар успішно збережений',
            'product' => new ProductResource($product)
        ]);
    }

    public function updateProduct(Request $request)
    {
        $data = $request->all();

        $product = Product::where('id', $data['id'])->first();
        $product->update([
            'user_id' => auth()->id(),
            'brand_id' => $data['brand_id'],
            'category_id' => $data['category_id'],
            'article' => $data['article'],
            'title' => $data['title'],
            'description' => $data['description'],
            'product_measure_id' => $data['product_measure_id'],
            'color' => $data['color'],
            'balance' => $data['balance'],
            'materials_used_quantity' => $data['materials_used_quantity'],
            'materials_used_measure_id' => $data['materials_used_measure_id'],
        ]);

        if (isset($data['items'])) {
            foreach ($data['items'] as $item) {
                ProductItem::updateOrCreate([
                    'id' => $item['id'],
                ], [
                    'product_id' => $data['id'],
                    'tax_id' => $item['tax_id'],
                    'cost_price' => $item['cost_price'],
                    'retail_price' => $item['retail_price'],
                ]);
            }
        }

        if ($product) {
            if ($request->hasFile('media')) {
                foreach ($data['media'] as $media) {
                    Media::create([
                        'type_id' => Media::PRODUCT_MEDIA,
                        'parent_id' => $product->id,
                        'file' => FileService::saveFile('uploads', "products", $media),
                    ]);
                }
            }
        }

        return $this->responseSuccess([
            'message' => 'Товар успішно відредагований',
            'product' => new ProductResource($product)
        ]);
    }

    public function getProductInfo(Request $request)
    {
        try {
            DB::beginTransaction();

            $brands = Brand::all();
            $categories = Category::all();
            $measurements = Measurement::all();
            $taxes = Taxes::all();
            $warehouses = Warehouse::all();


            DB::commit();

        } catch (\Exception $exception) {
            DB::rollBack();
            return back()->withErrors(["Помилка: {$exception->getMessage()}"]);
        }

        return $this->responseSuccess([
            'brands' => $brands,
            'categories' => $categories,
            'measurements' => $measurements,
            'taxes' => $taxes,
            'warehouses' => $warehouses,
        ]);
    }
}
