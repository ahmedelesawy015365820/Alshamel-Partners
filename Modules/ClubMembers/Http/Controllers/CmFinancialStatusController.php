<?php

namespace Modules\ClubMembers\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\ClubMembers\Http\Requests\CmFinancialStatusRequest;
use Modules\ClubMembers\Repositories\CmFinancialStatus\CmFinancialStatusInterface;
use Modules\ClubMembers\Transformers\CmFinancialStatusResource;

class CmFinancialStatusController extends Controller
{

    public function __construct(private CmFinancialStatusInterface $modelInterface)
    {
        $this->modelInterface = $modelInterface;
    }//

    public function all(Request $request)
    {
        $models = $this->modelInterface->all($request);

        return responseJson(200, 'success', CmFinancialStatusResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function find($id)
    {

        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        return responseJson(200, 'success', new CmFinancialStatusResource($model));
    }

    public function create(CmFinancialStatusRequest $request)
    {
        $model = $this->modelInterface->create($request);
        return responseJson(200, 'success', new CmFinancialStatusResource($model));
    }

    public function update(CmFinancialStatusRequest $request, $id)
    {
        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        $model = $this->modelInterface->update($request, $id);

        return responseJson(200, 'success', new CmFinancialStatusResource($model));
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

    public function logs($id)
    {
        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        $logs = $this->modelInterface->logs($id);
        return responseJson(200, 'success', $logs);
    }

    public function bulkDelete(Request $request)
    {
        foreach ($request->ids as $id) {
            $model = $this->modelInterface->find($id);

            $this->modelInterface->delete($id);
        }
        return responseJson(200, __('Done'));
    }

}
