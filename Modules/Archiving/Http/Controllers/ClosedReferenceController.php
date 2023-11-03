<?php

namespace Modules\Archiving\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Archiving\Http\Requests\ClosedReferenceRequest;
use Modules\Archiving\Repositories\ClosedReferenceInterface;
use Modules\Archiving\Transformers\ClosedReferenceResource;

class ClosedReferenceController extends Controller
{
    public function __construct(private ClosedReferenceInterface $modelInterface)
    {
        $this->modelInterface = $modelInterface;
    }

    public function all(Request $request)
    {
      
        $models = $this->modelInterface->all($request);
        return responseJson(200, 'success', ClosedReferenceResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function create(ClosedReferenceRequest $request)
    {
        $model = $this->modelInterface->create($request);
        return responseJson(200, 'success', new ClosedReferenceResource($model));
    }

    public function delete($id)
    {
        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, 'data not found');
        }

        if ($model->hasChildren()) {
            return responseJson(400, __("this item has children and can't be deleted remove it's children first"));
        }

        $this->modelInterface->delete($id);

        return responseJson(200, 'success');
    }

    public function bulkDelete(Request $request)
    {
        foreach ($request->ids as $id) {
            $model = $this->modelInterface->find($id);
            if (!$model) {
                return responseJson(404, 'data not found');
            }
            if ($model->hasChildren()) {
                return responseJson(400, __("this item has children and can't be deleted remove it's children first"));
            }
            $this->modelInterface->delete($id);
        }
        return responseJson(200, __('Done'));
    }

    public function update(ClosedReferenceRequest $request, $id)
    {
        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, 'data not found');
        }
        $this->modelInterface->update($request, $id);
        $model->refresh();
        return responseJson(200, 'success', new ClosedReferenceResource($model));
    }

    public function logs($id)
    {
        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }

        $logs = $model->activities()->orderBy('created_at', 'DESC')->get();
        return responseJson(200, 'success', \App\Http\Resources\Log\LogResource::collection($logs));
    }

}
