<?php

namespace App\Http\Controllers\SalesmenPlansSource;

use App\Http\Requests\SalesmenPlansSource\SalesmenPlansSourceRequest;
use App\Http\Resources\AllDropListResource;
use App\Http\Resources\SalesmenPlansSource\SalesmenPlansSourceResource;
use App\Repositories\SalesmenPlansSource\SalesmenPlansSourceInterface;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class SalesmenPlansSourceController extends Controller
{
    public function __construct(private SalesmenPlansSourceInterface $modelInterface)
    {
        $this->modelInterface = $modelInterface;
    }

    public function all(Request $request)
    {
        $models = $this->modelInterface->all($request);
        return responseJson(200, 'success', SalesmenPlansSourceResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function find($id)
    {

        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        return responseJson(200, 'success', new SalesmenPlansSourceResource($model));
    }

    public function create(SalesmenPlansSourceRequest $request)
    {
        $model = $this->modelInterface->create($request);
        $model->refresh();

        return responseJson(200, 'success', new SalesmenPlansSourceResource($model));
    }

    public function update(SalesmenPlansSourceRequest $request, $id)
    {
        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        $this->modelInterface->update($request, $id);
        $model->refresh();
        return responseJson(200, 'success', new SalesmenPlansSourceResource($model));
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

    public function getDropDown(Request $request)
    {

        $models = $this->modelInterface->getName($request);
        return responseJson(200, 'success', AllDropListResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

}
