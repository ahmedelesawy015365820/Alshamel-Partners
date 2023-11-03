<?php

namespace App\Http\Controllers;

use App\Http\Requests\GeneralItemRequest;
use App\Http\Resources\GeneralItemResource;
use App\Repositories\GeneralItem\GeneralItemInterface;
use Illuminate\Http\Request;

class GeneralItemController extends Controller
{
    public function __construct(private GeneralItemInterface $modelInterface)
    {
        $this->modelInterface = $modelInterface;
    }

    public function find($id)
    {

        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        return responseJson(200, 'success', new GeneralItemResource($model));
    }

    public function all(Request $request)
    {

        $models = $this->modelInterface->all($request);
        return responseJson(200, 'success', GeneralItemResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function create(GeneralItemRequest $request)
    {
        $model = $this->modelInterface->create($request);
        return responseJson(200, 'success');
    }

    public function update(GeneralItemRequest $request, $id)
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
            return responseJson(404, 'not found');
        }

        if ($model->hasChildren()) {
            return responseJson(404, 'not delete');
        }
        $model->delete();

        return responseJson(200, 'deleted');
    }

    public function bulkDelete(Request $request)
    {
        foreach ($request->ids as $id) {
            $model = $this->modelInterface->find($id);
            if (!$model->hasChildren()) {
                $this->modelInterface->delete($id);
            }
        }
        return responseJson(200, __('Done'));
    }
}
