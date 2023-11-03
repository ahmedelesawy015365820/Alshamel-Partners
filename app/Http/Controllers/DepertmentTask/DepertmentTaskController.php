<?php

namespace App\Http\Controllers\DepertmentTask;

use App\Http\Controllers\Controller;
use App\Http\Requests\DepertmentTask\DepertmentTaskRequest;
use App\Http\Resources\AllDropListResource;
use App\Http\Resources\DepertmentTask\DepertmentTaskResource;
use Illuminate\Http\Request;

class DepertmentTaskController extends Controller
{

    public function __construct(private \App\Repositories\DepertmentTask\DepertmentTaskInterface $modelInterface)
    {
        $this->modelInterface = $modelInterface;
    }

    public function find($id)
    {
        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        return responseJson(200, 'success', new DepertmentTaskResource($model));
    }

    public function all(Request $request)
    {
        $models = $this->modelInterface->all($request);
        return responseJson(200, 'success', DepertmentTaskResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function create(DepertmentTaskRequest $request)
    {
        $model = $this->modelInterface->create($request->validated());

        return responseJson(200, 'success');
    }

    public function update(DepertmentTaskRequest $request, $id)
    {
        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        $this->modelInterface->update($request->validated(), $id);
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
        // $model = $this->modelInterface->find($id);
        // if (!$model) {
        //     return responseJson(404, __('message.data not found'));
        // }
        // if ($model->hasChildren()) {
        //     return responseJson(400, __("this item has children and can't be deleted remove it's children first"));
        // }
        $this->modelInterface->delete($id);
        return responseJson(200, 'success');
    }

    public function bulkDelete(Request $request)
    {

        foreach ($request->ids as $id) {
            $model = $this->modelInterface->find($id);
            $arr = [];
            if ($model->hasChildren()) {
                $arr[] = $id;
                continue;
            }
            $this->modelInterface->delete($id);
        }
        if (count($arr) > 0) {
            return responseJson(400, __('some items has relation cant delete'));
        }
        return responseJson(200, __('Done'));
    }

    public function getDropDown(Request $request)
    {
        $models = $this->modelInterface->getName($request);
        return responseJson(200, 'success', AllDropListResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }
}
