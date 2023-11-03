<?php

namespace Modules\Stock\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Stock\Entities\StockSalePurchaseDetail;
use Modules\Stock\Http\Requests\OpenningBalanceRequest;
use Modules\Stock\Repositories\OpenningBalanceInterface;
use Modules\Stock\Transformers\OpenningBalanceResource;

class OpenningBalanceController extends Controller
{


    private $modelInterface;
    public function __construct(OpenningBalanceInterface $modelInterface)
    {
        $this->modelInterface = $modelInterface;
    }

    public function all(Request $request)
    {
        if (count($_GET) == 0) {
            $models = cacheGet('openinngBalance');
            if (!$models) {
                $models = $this->modelInterface->all($request);
                cachePut('openinngBalance', $models);
            }
        } else {
            $models = $this->modelInterface->all($request);
        }
        return responseJson(200, 'success', OpenningBalanceResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function getAll(Request $request)
    {
        $models = $this->modelInterface->getAll($request);
        return responseJson(200, 'success', OpenningBalanceResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }
    public function create(OpenningBalanceRequest $request)
    {
        $this->modelInterface->create($request);
        return responseJson(200, 'success');
    }

    public function delete($id)
    {

        $wallet = StockSalePurchaseDetail::where('wallet_id', $id)->first();
        // dd($wallet)
        if (empty($wallet)) {
            $model = $this->modelInterface->delete($id);
            if (!$model) {
                return responseJson(404, 'data not found');
            }
            return responseJson(200, 'success');
        } else {
            $stockError = 'You cannot delete this wallet entry because it has some sale/purchase transaction. Kindly delete the transactions first and then delete this entry';
            $models['stock'] = $stockError;
            $models['data'] = false;
            return responseJson(200, $models['stock'], $models['data']);
        }


        return responseJson(200, 'success');
    }

    public function bulkDelete(Request $request)
    {
        foreach ($request->ids as $id) {
            $this->modelInterface->delete($id);
        }
        return responseJson(200, __('Done'));
    }

    public function rowUpdate(Request $request)
    {

        $model = $this->modelInterface->rowUpdate($request);
        if (!$model) {
            return responseJson(404, 'data not found');
        }
        return responseJson(200, 'success');
    }
    public function getWalletEntier($id)
    {
        $models = $this->modelInterface->getWalletEntier($id);
        return responseJson(200, 'success', OpenningBalanceResource::collection($models['data']));
    }

    public function checkDate($id)
    {
        $models = $this->modelInterface->checkDate($id);
        if ($models['data'] == false) {
            return responseJson(200, $models['data']);
        }
        return responseJson(200, 'success', $models['data']);
    }
}
