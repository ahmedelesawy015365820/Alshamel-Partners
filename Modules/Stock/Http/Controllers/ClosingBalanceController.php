<?php

namespace Modules\Stock\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Stock\Http\Requests\ClosingBalanceRequest;
use Modules\Stock\Repositories\ClosingBalanceInterface;
use Modules\Stock\Transformers\ClosingBalanceResource;

class ClosingBalanceController extends Controller
{
    protected $modelInterface;
    public function __construct(ClosingBalanceInterface $modelInterface)
    {
        $this->modelInterface = $modelInterface;
    }

    public function all(Request $request)
    {
        if (count($_GET) == 0) {
            $models = cacheGet('closingBalance');
            if (!$models) {
                $models = $this->modelInterface->all($request);
                cachePut('closingBalance', $models);
            }
        } else {
            $models = $this->modelInterface->all($request);
        }

        return responseJson(200, 'success', ClosingBalanceResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function create(ClosingBalanceRequest $request)
    {
        $this->modelInterface->create($request);
        return responseJson(200, 'success');
    }

    public function delete($date)
    {
        $model =  $this->modelInterface->delete($date);
        if (!$model) {
            return responseJson(404, 'data not found');
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

    public function getAllEntier($date)
    {
        $models = $this->modelInterface->getAllEntier($date);
        return responseJson(200, 'success', ClosingBalanceResource::collection($models['data']));
    }
}
