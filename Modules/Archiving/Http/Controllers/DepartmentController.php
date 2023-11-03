<?php

namespace Modules\Archiving\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Archiving\Entities\DocumentField;
use Modules\Archiving\Http\Requests\DepartmentRequest;
use Modules\Archiving\Repositories\DepartmentInterface;
use Modules\Archiving\Transformers\DepartmentDocTypeResource;
use Modules\Archiving\Transformers\DepartmentResource;
use Modules\Archiving\Transformers\NewDepartmentResource;

class DepartmentController extends Controller
{

    public function __construct(private DepartmentInterface $modelInterface)
    {
        $this->modelInterface = $modelInterface;
    }

    public function all(Request $request)
    {

        $models = $this->modelInterface->all($request);
        return responseJson(200, 'success', NewDepartmentResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function parentDepartment(Request $request){
        $models = $this->modelInterface->parentDepartment($request);
        return responseJson(200, 'success', DepartmentDocTypeResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function tree(Request $request)
    {
        $models = $this->modelInterface->tree($request);
        return responseJson(200, 'success', DepartmentResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function onlyHasKey(Request $request)
    {
        $models = $this->modelInterface->onlyHasKey($request);
        return responseJson(200, 'success', DepartmentResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function create(DepartmentRequest $request)
    {
        $model = $this->modelInterface->create($request);

        return responseJson(200, 'success', new NewDepartmentResource($model));
    }

    public function delete($id)
    {
        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, 'data not found');
        }
        $key_val = DocumentField::query()->where('id', $model->key_value)->first();
        if ($model->hasChildren() || $key_val) {
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
                return responseJson(400, __("message.this item has children and can't be deleted remove it's children first"));
            }
            $this->modelInterface->delete($id);
        }
        return responseJson(200, __('Done'));
    }

    public function update(DepartmentRequest $request, $id)
    {
        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, 'data not found');
        }
        $this->modelInterface->update($request, $id);
        $model->refresh();
        return responseJson(200, 'success', new NewDepartmentResource($model));
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
