<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\DeleteProductRequest;
use App\Http\Requests\Product\GetProductRequest;
use App\Http\Requests\Product\SaveProductMedia;
use App\Http\Requests\Product\SaveProductMediaRequest;
use App\Http\Requests\Product\SaveProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
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
use App\Models\Vendor;
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
                Route::get('get-product', [static::class, 'getProduct']);
                Route::get('get-products', [static::class, 'getProducts']);
                Route::post('add-product', [static::class, 'addProduct']);
                Route::post('update-product', [static::class, 'updateProduct']);
                Route::delete('delete-product', [static::class, 'deleteProduct']);

                Route::get('get-product-info', [static::class, 'getProductInfo']);

                Route::post('save-product-media', [static::class, 'saveProductMedia']);
                Route::delete('delete-product-media', [static::class, 'deleteProductMedia']);
            }
        );
    }

    public function getProduct(GetProductRequest $request)
    {
        $data = $request->all();
        $product = Product::where('id', $data['id'])
            ->where('user_id', auth()->id())
            ->first();

        if (empty($product)) {
            return $this->responseError('Товар не знайдений');
        }

        return $this->responseSuccess([
            'product' => new ProductResource($product)
        ]);
    }

    public function getProducts(Request $request)
    {
        $data = $request->all();
        $builder = Product::query();
        $builder->where('user_id', auth()->id());
        $this->setSorting($builder, [
            'id' => 'id',
        ]);

        if (isset($data['category_id'])){
            $builder->where('category_id', $data['category_id']);
        }

        $products = $builder->paginate($this->getPerPage($data['perPage'] ?? 15));

        return $this->responseSuccess(new ProductsResource($products, false));
    }

    public function addProduct(SaveProductRequest $request)
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
            'cost_price' => $data['cost_price'],
            'retail_price' => $data['retail_price'],
            'tags' => isset($data['tags']) ? json_encode($data['tags']) : null,
            'vendors' => isset($data['vendors']) ? json_encode($data['vendors']) : null,
            'materials_used_quantity' => isset($data['materials_used_quantity']) ? $data['materials_used_quantity'] : null,
            'materials_used_measure_id' => isset($data['materials_used_measure_id']) ? $data['materials_used_measure_id'] : null,
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

    public function updateProduct(UpdateProductRequest $request)
    {
        $data = $request->all();

        $product = Product::where('id', $data['id'])->first();
        if ($product) {
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
                'cost_price' => $data['cost_price'],
                'retail_price' => $data['retail_price'],
                'tags' => isset($data['tags']) ? json_encode($data['tags']) : null,
                'vendors' => isset($data['vendors']) ? json_encode($data['vendors']) : null,
                'materials_used_quantity' => isset($data['materials_used_quantity']) ? $data['materials_used_quantity'] : null,
                'materials_used_measure_id' => isset($data['materials_used_measure_id']) ? $data['materials_used_measure_id'] : null,
            ]);
        }

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

    public function deleteProduct(DeleteProductRequest $request)
    {
        $data = $request->all();

        ProductItem::whereIn('product_id', $data['idx'])->delete();
        Media::where('type_id', Media::PRODUCT_MEDIA)->whereIn('parent_id', $data['idx'])->delete();
        Product::whereIn('id', $data['idx'])->delete();

        return $this->responseSuccess([
            'message' => 'Товар успешно удален'
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
            $vendors = Vendor::all();

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
            'vendors' => $vendors
        ]);
    }

    public function saveProductMedia(SaveProductMediaRequest $request)
    {
        $data = $request->all();
        $product = Product::find($data['product_id']);

        if (empty($product)) {
            return $this->responseError('Товар не знайдений');
        }

        if ($product) {
            if ($request->hasFile('media')) {
                foreach ($data['media'] as $media) {
                    Media::create([
                        'type_id' => Media::PRODUCT_MEDIA,
                        'parent_id' => $product->id,
                        'file' => FileService::saveFile('uploads', "media", $media),
                    ]);
                }
            }
        }

        return $this->responseSuccess([
            'product' => new ProductResource($product),
        ]);
    }

    public function deleteProductMedia(Request $request)
    {
        $data = $request->all();
        $media = Media::where('type_id', Media::PRODUCT_MEDIA)
            ->where('id', $data['id'])
            ->first();

        if ($media) {
            FileService::removeFile('uploads', 'media', $media->file);

            $media->delete();
        }

        return $this->responseSuccess([
            'message' => 'Медіа успішно видалений'
        ]);
    }
}
