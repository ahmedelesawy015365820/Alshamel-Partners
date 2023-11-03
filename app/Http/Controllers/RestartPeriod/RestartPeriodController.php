<?php

namespace App\Http\Controllers\RestartPeriod;

use App\Http\Requests\RestartPeriodRequest;
use App\Http\Resources\AllDropListResource;
use App\Http\Resources\RestartPeriodResource;
use App\Models\RestartPeriod;
use App\Repositories\RestartPeriod\RestartPeriodInterface;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class RestartPeriodController extends Controller
{
    public function __construct(private RestartPeriodInterface $modelInterface)
    {
        $this->modelInterface = $modelInterface;
    }

    public function all(Request $request)
    {

        $models = $this->modelInterface->all($request);
        return responseJson(200, 'success', RestartPeriodResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function find($id)
    {

        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        return responseJson(200, 'success', new RestartPeriodResource($model));
    }

    public function create(RestartPeriodRequest $request)
    {
        $model = $this->modelInterface->create($request);
        $model->refresh();

        return responseJson(200, 'success');
    }

    public function update(RestartPeriodRequest $request, $id)
    {
        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        $this->modelInterface->update($request, $id);
        $model->refresh();
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

    public function getRestartPeriodData()
    {
        $ids = [3, 4, 5, 7];

        $periods = RestartPeriod::whereIn('id', $ids)->get();

        return responseJson(200, $periods);
    }

    public function getRestartPeriodInSerial()
    {
        $ids = [5, 6];

        $periods = RestartPeriod::whereIn('id', $ids)->get();

        return responseJson(200, $periods);
    }

    public function getDropDown(Request $request)
    {
        $models = $this->modelInterface->getName($request);
        return responseJson(200, 'success', AllDropListResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

}
