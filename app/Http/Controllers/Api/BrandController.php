<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Brand\GetBrandRequest;
use App\Http\Requests\Brand\StoreBrandRequest;
use App\Http\Requests\Brand\UpdateBrandRequest;
use App\Http\Resources\Brand\BrandResource;
use App\Http\Resources\Brand\BrandsResource;
use App\Http\Resources\Traits\HasFullInfoFlag;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Route;

class BrandController extends CoreController
{
    public static function routers()
    {
        Route::group(
            [
                'prefix' => 'brand',
                'middleware' => 'auth:api'
            ],
            function () {
                Route::get('get-all-brands', [static::class, 'getBrands']);
                Route::get('get-brand', [static::class, 'getBrand']);

                Route::post('add-brand', [static::class, 'addBrand']);
                Route::post('edit-brand', [static::class, 'editBrand']);
                Route::delete('delete-brand', [static::class, 'deleteBrand']);
            }
        );
    }

    public function getBrands(Request $request)
    {
        $brands = Brand::all();

        return $this->responseSuccess([
            'brands' => new BrandsResource($brands)
        ]);
    }

    public function getBrand(GetBrandRequest $request)
    {
        $data = $request->all();
        $brand = Brand::find($data['id']);

        return $this->responseSuccess([
            'brand' => new BrandResource($brand)
        ]);
    }

    public function addBrand(StoreBrandRequest $request)
    {
        $data = $request->all();
        $brand = Brand::create([
           'title' => $data['title'],
           'description' => $data['description']
        ]);

        return $this->responseSuccess([
            'brands' => new BrandResource($brand)
        ]);
    }

    public function editBrand(UpdateBrandRequest $request)
    {
        $data = $request->all();
        $brand = Brand::find($data['id']);
        $brand->update([
            'title' => $data['title'],
            'description' => $data['description']
        ]);

        return $this->responseSuccess([
            'brands' => new BrandResource($brand)
        ]);
    }

    public function deleteBrand(GetBrandRequest $request)
    {
        $data = $request->all();
        $brand = Brand::find($data['id']);
        if ($brand){
            $brand->delete();
        }

        return $this->responseSuccess([
            'message' => 'Бренд успішно видалений'
        ]);
    }
}
