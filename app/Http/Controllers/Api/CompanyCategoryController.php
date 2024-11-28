<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductCategory\ProductCategoryCreateRequest;
use App\Http\Requests\ProductCategory\ProductCategoryDeleteRequest;
use App\Http\Requests\ProductCategory\ProductCategoryUpdateRequest;
use App\Http\Resources\Category\CategoryResource;
use App\Models\Category;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Route;

class CompanyCategoryController extends CoreController
{
    public static function routers()
    {
        Route::group(
            [
                'prefix' => 'company-category',
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
        $category = Category::create([
            'title' => $data['title']
        ]);

        return $this->responseSuccess([
            'message' => 'Категорія кампанії успішно збережена',
            'category' => new CategoryResource($category),
        ]);
    }

    public function editCategory(ProductCategoryUpdateRequest $request)
    {
        $data = $request->all();
        $category = Category::where('id', $data['id'])->first();
        $category->update([
            'title' => $data['title']
        ]);

        return $this->responseSuccess([
            'message' => 'Категорія кампанії успішно відредагована',
            'category' => new CategoryResource($category),
        ]);
    }

    public function deleteCategory(ProductCategoryDeleteRequest $request)
    {
        $data = $request->all();
        Category::where('id', $data['id'])->delete();

        return $this->responseSuccess([
            'message' => 'Категорія кампанії успішно видалена',
        ]);
    }
}
