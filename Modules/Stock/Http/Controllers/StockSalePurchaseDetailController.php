<?php

namespace Modules\Stock\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Stock\Http\Requests\StockSalePurchaseDetailRequest;
use Modules\Stock\Repositories\StockSalePurchaseDetailInterface;
use Modules\Stock\Transformers\StockSalePurchaseDetailResource;

class StockSalePurchaseDetailController extends Controller
{
    private $modelInterface;
    public function __construct(StockSalePurchaseDetailInterface $modelInterface)
    {
        $this->modelInterface = $modelInterface;
    }

    public function all(Request $request)
    {
        if (count($_GET) == 0) {
            $models = cacheGet('stockSalePurchaseDetail');
            if (!$models) {
                $models = $this->modelInterface->all($request);
                cachePut('stockSalePurchaseDetail', $models);
            }
        } else {
            $models = $this->modelInterface->all($request);
        }

        return responseJson(200, 'success', StockSalePurchaseDetailResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function updataData(StockSalePurchaseDetailRequest $request)
    {
        $last_record = DB::table('stock_sale_purchase_details')->where("wallet_id", $request->wallet_id)->where('id', '!=', $request->id)->latest('id')->first();
        if (!is_null($last_record)) {
            if (((int)$request->input('qty') > $last_record->new_qty) && $request->input('type') == "Sell") {
                return responseJson(202, 'Quantity');
            }
        } else {
            if ($request->input('type') == "Sell") {
                return responseJson(203, 'Quantity');
            }
        }
        $this->modelInterface->updataData($request);
        return responseJson(200, 'success');
    }

    public function updaterow(Request $request)
    {
        $model = $this->modelInterface->find($request->id);

        return responseJson(200, 'success', $model);
    }

    public function create(StockSalePurchaseDetailRequest $request)
    {
        $last_record = DB::table('stock_sale_purchase_details')->where("wallet_id", $request->wallet_id)->where("stock_id", $request->stock_id)->latest('id')->first();
        if (!is_null($last_record)) {
            if (((int)$request->input('qty') > $last_record->new_qty) && $request->input('type') == "Sell") {
                return responseJson(202, 'Quantity');
            }
        } else {
            if ($request->input('type') == "Sell") {
                return responseJson(203, 'Quantity');
            }
        }
        $this->modelInterface->create($request);
        return responseJson(200, 'success');
    }

    public function deleteData(Request $request)
    {
        $model = $this->modelInterface->find($request->id);
        if (!$model) {
            return responseJson(404, 'data not found');
        }
        $this->modelInterface->delete($request->id);

        return responseJson(200, 'success');
    }

    public function bulkDelete(Request $request)
    {
        foreach ($request->ids as $id) {
            $this->modelInterface->delete($id);
        }
        return responseJson(200, __('Done'));
    }

    public function update(StockSalePurchaseDetailRequest $request, $id)
    {
        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, 'data not found');
        }
        $this->modelInterface->update($request, $id);
        return responseJson(200, 'success');
    }
}
