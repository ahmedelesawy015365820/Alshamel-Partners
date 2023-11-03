<?php

namespace App\Http\Controllers\ExternalSalesmen;

use App\Http\Requests\ExternalSalesmen\ExternalSalesmenRequest;
use App\Http\Requests\ExternalSalesmen\UpdateExternalSalesmenRequest;
use App\Http\Resources\ExternalSalesmen\ExternalSalesmenResource;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ExternalSalesmenController extends Controller
{
    public function __construct(private \App\Repositories\ExternalSalesmen\ExternalSalesmenInterface $modelInterface)
    {
        $this->modelInterface = $modelInterface;
    }

    public function find($id)
    {

        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        return responseJson(200, 'success', new ExternalSalesmenResource($model));
    }

    public function all(Request $request)
    {

        $models = $this->modelInterface->all($request);

        return responseJson(200, 'success', ExternalSalesmenResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function create(ExternalSalesmenRequest $request)
    {
        $model = $this->modelInterface->create($request);
        return responseJson(200, 'success');
    }

    public function update(ExternalSalesmenRequest $request, $id)
    {
        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        $model = $this->modelInterface->update($request, $id);

        return responseJson(200, 'success');
    }

    public function logs($id)
    {
        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }

        $logs = $this->modelInterface->logs($id);
        return responseJson(200, 'success', \App\Http\Resources\Log\LogResource::collection($logs));

    }

    public function delete($id)
    {
        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }

        $this->modelInterface->delete($id);

        return responseJson(200, 'success');
    }

    public function bulkDelete(Request $request)
    {
        foreach ($request->ids as $id) {
            $this->modelInterface->delete($id);
        }
        return responseJson(200, __('Done'));
    }

}
