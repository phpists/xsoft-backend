<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Category\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class ProductCategoryController extends CoreController
{
    public static function routers()
    {
        Route::group(
            [
                'prefix' => 'category',
                'middleware' => 'auth:api'
            ],
            function () {
                Route::post('add-category', [static::class, 'addCategory']);
            }
        );
    }

    public function addCategory(Request $request)
    {
        $data = $request->all();
        $category = Category::create([
            'title' => $data['title']
        ]);

        return $this->responseSuccess([
           'category' => new CategoryResource($category),
        ]);
    }
}
