<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CashCategory\UpdateCashCategoryRequest;
use App\Http\Requests\Cashes\GetCasheById;
use App\Http\Requests\Cashes\SaveCashesRequest;
use App\Http\Requests\Cashes\UpdateCashesRequest;
use App\Http\Resources\Cashes\CasheResource;
use App\Http\Resources\Cashes\CashesHistoryItemResource;
use App\Http\Resources\Cashes\CashesHistoryResource;
use App\Http\Resources\Cashes\CashesResource;
use App\Models\Cashes;
use App\Models\CashesCategory;
use App\Models\CashesHistory;
use App\Models\ProductMovement;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Route;

class CashesController extends CoreController
{
    /**
     * Каси
     */
    public static function routers()
    {
        Route::group(
            [
                'prefix' => 'cashes',
                'middleware' => 'auth:api'
            ],
            function () {
                Route::get('get-cashes', [static::class, 'getAllCashes']);
                Route::get('get-cash', [static::class, 'getCash']);
                Route::post('add-cash', [static::class, 'addCash']);
                Route::post('edit-cash', [static::class, 'editCash']);
                Route::delete('delete-cash', [static::class, 'deleteCash']);


                Route::get('get-cash-transactions', [static::class, 'getCashTransactions']);

                Route::get('get-debt', [static::class, 'getDebt']);
                Route::post('debt-paid', [static::class, 'debtPaid']);
                Route::post('cancel-debt-paid', [static::class, 'cancelDebtPaid']);
            }
        );
    }

    public function getAllCashes()
    {
        $auth = User::find(auth()->id());
        $cashes = Cashes::where('company_id', $auth->getCurrentCompanyId())
            ->get();

        return $this->responseSuccess([
            'cashes' => new CashesResource($cashes)
        ]);
    }

    public function getCash(GetCasheById $request)
    {
        $data = $request->all();
        $auth = User::find(auth()->id());
        $cashes = Cashes::where('company_id', $auth->getCurrentCompanyId())
            ->where('id', $data['id'])
            ->first();

        return $this->responseSuccess([
            'cashes' => new CasheResource($cashes)
        ]);
    }

    public function addCash(SaveCashesRequest $request)
    {
        $data = $request->all();
        $auth = User::find(auth()->id());

        $cashe = Cashes::create([
            'company_id' => $auth->getCurrentCompanyId(),
            'title' => $data['title'],
            'appointment' => $data['appointment'],
            'description' => $data['description'],
            'is_cash_category' => $data['is_cash_category']
        ]);

        if ($cashe && $data['cash_categories']) {
            foreach ($data['cash_categories'] as $cashCategoryId) {
                CashesCategory::updateOrCreate([
                    'cashes_id' => $cashe->id,
                    'cash_category_id' => $cashCategoryId
                ], [
                    'cashes_id' => $cashe->id,
                    'cash_category_id' => $cashCategoryId
                ]);
            }
        }

        return $this->responseSuccess([
            'message' => 'Каса успішно збережена',
            'cashes' => new CasheResource($cashe)
        ]);
    }

    public function editCash(UpdateCashesRequest $request)
    {
        $data = $request->all();
        $auth = User::find(auth()->id());
        $cashe = Cashes::find($data['id']);

        $cashe->update([
            'company_id' => $auth->getCurrentCompanyId(),
            'title' => $data['title'],
            'appointment' => $data['appointment'],
            'description' => $data['description'],
            'is_cash_category' => $data['is_cash_category']
        ]);

        if ($cashe && $data['cash_categories']) {
            CashesCategory::where('cashes_id', $cashe->id)->delete();
            foreach ($data['cash_categories'] as $cashCategoryId) {
                CashesCategory::updateOrCreate([
                    'cashes_id' => $cashe->id,
                    'cash_category_id' => $cashCategoryId
                ], [
                    'cashes_id' => $cashe->id,
                    'cash_category_id' => $cashCategoryId
                ]);
            }
        }

        return $this->responseSuccess([
            'message' => 'Каса успішно відредагована',
            'cashes' => new CasheResource($cashe)
        ]);
    }

    public function deleteCash(GetCasheById $request)
    {
        $data = $request->all();
        $cashe = Cashes::find($data['id']);
        if ($cashe) {
            CashesCategory::where('cashes_id', $cashe->id)->delete();
            $cashe->delete();
        }

        return $this->responseSuccess([
            'message' => 'Каса успішно видалена',
        ]);
    }

    public function getCashTransactions(GetCasheById $request)
    {
        $data = $request->all();
        $query = CashesHistory::query();
        $query->where('cashes_id', $data['id']);

        if (isset($data['debt_status']) && $data['debt_status'] === 'true') {
            $query->where('type_id', ProductMovement::DEBT);
        }

        if (isset($data['time_period'])) {
            $typeId = $data['time_period']; // 1 - за день, 2 - за тиждень, 3 - за місяць, 4 - за рік
            $currentDate = now();

            switch ($typeId) {
                case 1: // За день
                    $query->whereDate('created_at', $currentDate);
                    break;
                case 2: // За тиждень
                    $query->whereBetween('created_at', [
                        $currentDate->copy()->startOfWeek(),
                        $currentDate->copy()->endOfWeek()
                    ]);
                    break;
                case 3: // За місяць
                    $query->whereBetween('created_at', [
                        $currentDate->copy()->startOfMonth(),
                        $currentDate->copy()->endOfMonth()
                    ]);
                    break;
                case 4: // За рік
                    $query->whereBetween('created_at', [
                        $currentDate->copy()->startOfYear(),
                        $currentDate->copy()->endOfYear()
                    ]);
                    break;
            }
        }

        if (isset($data['date_start']) && isset($data['date_end'])) {
            $query->whereBetween('created_at', [
                Carbon::parse($data['date_start'])->startOfDay(),
                Carbon::parse($data['date_end'])->endOfDay()
            ]);
        }

        $transactions = $query->get();

        $balanceStart = CashesHistory::where('cashes_id', $data['id'])
                ->where('created_at', '<', $transactions->first()?->created_at)
                ->orderBy('created_at', 'desc')
                ->value('amount') ?? 0;

        $balanceEnd = $transactions->last()?->amount ?? $balanceStart;

        $totalProfit = $transactions->where('type_id', ProductMovement::SALE)->sum('amount');
        $totalLoss = $transactions->where('type_id', ProductMovement::WRITE_DOWN)->sum('amount');

        return $this->responseSuccess([
            'transactions' => new CashesHistoryResource($transactions),
            'statistic' => [
                'balance_start' => $balanceStart, // Баланс на початок періоду
                'balance_end' => $balanceEnd, // Баланс на кінець періоду
                'total_profit' => $totalProfit, // Дохід за період (type_id = 2)
                'total_loss' => $totalLoss, // Витрати за період (type_id = 3)
            ],
        ]);
    }


    /**
     * Отримати борг по id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDebt(Request $request)
    {
        $data = $request->all();
        $transaction = CashesHistory::where('id', $data['id'])
            ->where('type_id', ProductMovement::DEBT)
            ->first();

        if (empty($transaction)) {
            return $this->responseError('Борг не знайдений');
        }

        return $this->responseSuccess([
            'transaction' => new CashesHistoryItemResource($transaction)
        ]);
    }

    /**
     * Оплатити борг
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function debtPaid(Request $request)
    {
        $data = $request->all();
        $cashesHistory = CashesHistory::where('id', $data['id'])
            ->where('type_id', ProductMovement::DEBT)
            ->first();

        if (empty($cashesHistory)) {
            return $this->responseError('Борг не знайдений');
        }

        $cashes = Cashes::find($cashesHistory->cashes_id);

        if (isset($cashesHistory) && isset($cashes)) {

            if ($cashes->total <= 0) {
                return $this->responseError('Недостатньо коштів для списання. На рахунку: ' . $cashes->total);
            }

            if ($cashes->total <= $cashes->debt) {
                return $this->responseError('Недостатньо коштів для списання. На рахунку: ' . $cashes->total);
            }

            $cashes->debt -= $cashesHistory->amount;
            $cashes->update();

            $cashesHistory->type_id = ProductMovement::DEBT_PAID;
            $cashesHistory->update();

            return $this->responseSuccess([
                'status' => true,
                'message' => 'Борг успішно оплачений'
            ]);
        } else {
            return $this->responseError('Борг не знайдений');
        }
    }

    /**
     * Відмінити оплату боргу
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancelDebtPaid(Request $request)
    {
        $data = $request->all();
        $cashesHistory = CashesHistory::where('id', $data['id'])
            ->where('type_id', ProductMovement::DEBT)
            ->first();

        if (empty($cashesHistory)) {
            return $this->responseError('Борг не знайдений');
        }

        $cashes = Cashes::find($cashesHistory->cashes_id);
        if ($cashes) {
            $cashes->debt += $cashesHistory->amount;
            $cashes->update();
        }

        $cashesHistory->type_id = ProductMovement::DEBT_PAID;
        $cashesHistory->update();

        return $this->responseSuccess([
            'status' => true,
            'message' => 'Борг успішно відмінений'
        ]);
    }
}
