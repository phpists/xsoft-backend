<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CashCategory\DeleteCashCategoryRequest;
use App\Http\Requests\CashCategory\SaveCashCategoryRequest;
use App\Http\Requests\CashCategory\UpdateCashCategoryRequest;
use App\Http\Resources\CashCategory\CashCategoriesResource;
use App\Http\Resources\CashCategory\CashCategoryResource;
use App\Http\Resources\Traits\HasFullInfoFlag;
use App\Models\CashCategory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Route;

class CashCategoryController extends CoreController
{
    public static function routers()
    {
        Route::group(
            [
                'prefix' => 'cash-category',
                'middleware' => 'auth:api',
            ],
            function () {
                Route::get('get-cash-categories', [static::class, 'getCashCategories']);
                Route::post('add-cash-category', [static::class, 'addCashCategory']);
                Route::post('update-cash-category', [static::class, 'updateCashCategory']);
                Route::delete('delete-cash-categories', [static::class, 'deleteCashCategories']);
            }
        );
    }

    public function getCashCategories(Request $request)
    {
        $auth = User::find(auth()->id());
        $cashCategories = CashCategory::where('company_id', $auth->getCurrentCompanyId())
            ->get();

        return $this->responseSuccess([
            'cash_categories' => new CashCategoriesResource($cashCategories)
        ]);
    }

    public function addCashCategory(SaveCashCategoryRequest $request)
    {
        $data = $request->all();
        $auth = User::find(auth()->id());

        $cashCategory = CashCategory::create([
            'company_id' => $auth->getCurrentCompanyId(),
            'title' => $data['title'],
            'type_id' => $data['type_id'],
        ]);

        return $this->responseSuccess([
            'message' => 'Стаття успішно збережена',
            'cash_category' => new CashCategoryResource($cashCategory)
        ]);
    }

    public function updateCashCategory(UpdateCashCategoryRequest $request)
    {
        $data = $request->all();
        $auth = User::find(auth()->id());

        $cashCategory = CashCategory::find($data['id']);
        $cashCategory->update([
            'company_id' => $auth->getCurrentCompanyId(),
            'title' => $data['title'],
            'type_id' => $data['type_id'],
        ]);

        return $this->responseSuccess([
            'message' => 'Стаття успішно відредагована',
            'cash_category' => new CashCategoryResource($cashCategory)
        ]);
    }

    public function deleteCashCategories(DeleteCashCategoryRequest $request)
    {
        $data = $request->all();
        CashCategory::whereIn('id', $data['idx'])->delete();

        return $this->responseSuccess([
            'message' => 'Статті успішно видалені'
        ]);
    }
}
