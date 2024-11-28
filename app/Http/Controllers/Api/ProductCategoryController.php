<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\ProductCategory\ProductCategoryCreateRequest;
use App\Http\Requests\ProductCategory\ProductCategoryDeleteRequest;
use App\Http\Requests\ProductCategory\ProductCategoryUpdateRequest;
use App\Http\Resources\Category\CategoriesResource;
use App\Http\Resources\Category\CategoryResource;
use App\Models\Category;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class ProductCategoryController extends CoreController
{
    public static function routers()
    {
        Route::group(
            [
                'prefix' => 'product-category',
                'middleware' => 'auth:api'
            ],
            function () {
                Route::post('add-category', [static::class, 'addCategory']);
                Route::post('edit-category', [static::class, 'editCategory']);
                Route::delete('delete-category', [static::class, 'deleteCategory']);
            }
        );
    }

    public function addCategory(ProductCategoryCreateRequest $request)
    {
        $data = $request->all();
        $category = ProductCategory::create([
            'title' => $data['title']
        ]);

        return $this->responseSuccess([
            'message' => 'Категорія товарів успішно збережена',
            'category' => new CategoryResource($category),
        ]);
    }

    public function editCategory(ProductCategoryUpdateRequest $request)
    {
        $data = $request->all();
        $category = ProductCategory::where('id', $data['id'])->first();
        $category->update([
            'title' => $data['title']
        ]);

        return $this->responseSuccess([
            'message' => 'Категорія товарів успішно відредагована',
            'category' => new CategoryResource($category),
        ]);
    }

    public function deleteCategory(ProductCategoryDeleteRequest $request)
    {
        $data = $request->all();
        ProductCategory::where('id', $data['id'])->delete();

        return $this->responseSuccess([
            'message' => 'Категорія товарів успішно видалена',
        ]);
    }
}
