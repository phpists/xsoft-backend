<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Company\SetCompanyRequest;
use App\Http\Requests\Company\StoreCompanyRequest;
use App\Http\Requests\Company\UpdateCompanyRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Http\Resources\Company\CompaniesResource;
use App\Http\Resources\Company\CompanyResource;
use App\Http\Resources\User\UserResource;
use App\Models\Company;
use App\Models\CompanyBranches;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Route;

class CompanyController extends CoreController
{
    public static function routers()
    {
        Route::group(
            [
                'prefix' => 'company',
                'middleware' => 'auth:api'
            ],
            function () {
                Route::get('get-companies', [static::class, 'getCompanies']);
                Route::post('add-company', [static::class, 'addCompany']);
                Route::post('edit-company', [static::class, 'editCompany']);
                Route::delete('delete-company', [static::class, 'deleteCompany']);

                Route::post('set-company', [static::class, 'setCompany']);
            }
        );
    }

    public function getCompanies(Request $request)
    {
        $companies = Company::where('user_id', auth()->id())
            ->get();

        return $this->responseSuccess(new CompaniesResource($companies));
    }

    public function addCompany(StoreCompanyRequest $request)
    {
        $data = $request->all();
        $company = Company::create([
            'title' => $data['title'],
            'user_id' => auth()->id(),
            'category_id' => $data['category_id'],
            'color' => $data['color'],
        ]);

        if ($company) {
            if (count($data['locations'])) {
                foreach ($data['locations'] as $location) {
                    CompanyBranches::create([
                        'company_id' => $company->id,
                        'title' => $location['name'],
                        'location' => $location['title'],
                        'phones' => isset($location['phones']) ? json_encode($location['phones']) : null,
                    ]);
                }
            }
        }

        return $this->responseSuccess([
            'message' => 'Компація успішно створена',
            'company' => new CompanyResource($company)
        ]);
    }

    public function editCompany(UpdateCompanyRequest $request)
    {
        $data = $request->all();
        $company = Company::find($data['id']);
        $company->update([
            'title' => $data['title'],
            'user_id' => auth()->id(),
            'category_id' => $data['category_id'],
            'color' => $data['color'],
        ]);

        if (count($data['locations'])) {
            foreach ($data['locations'] as $location) {
                CompanyBranches::updateOrCreate([
                    'id' => $location['id']
                ], [
                    'company_id' => $company->id,
                    'title' => $location['name'],
                    'location' => $location['title'],
                    'phones' => isset($location['phones']) ? json_encode($location['phones']) : null,
                ]);
            }
        }

        return $this->responseSuccess([
            'message' => 'Компація успішно відредагована',
            'company' => new CompanyResource($company)
        ]);
    }

    public function deleteCompany(Request $request)
    {
        $data = $request->all();
        Company::where('user_id', auth()->id())
            ->where('id', $data['id'])
            ->delete();

        return $this->responseSuccess([
            'message' => 'Компанія успішно видалена'
        ]);
    }

    /**
     * Встановити копанію по замовчуванню
     * @param SetCompanyRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function setCompany(SetCompanyRequest $request)
    {
        $data = $request->all();
        $user = User::find(auth()->id());

        if (empty($user)){
            return $this->responseError('User not found');
        }

        $user->update([
            'company_id' => $data['company_id']
        ]);

        return $this->responseSuccess([
            'message' => 'Компанія успішно встановлена',
            'user' => new UserResource($user),
        ]);
    }
}
