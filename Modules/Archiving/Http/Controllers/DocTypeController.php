<?php

namespace Modules\Archiving\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Archiving\Entities\DocType;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Archiving\Http\Requests\DocTypeRequest;
use Modules\Archiving\Transformers\DocTypeResource;
use Modules\Archiving\Repositories\DocTypeInterface;
use Modules\Archiving\Transformers\AllDocTypeResource;
use Modules\Archiving\Transformers\NewDocTypeResource;
use Modules\Archiving\Transformers\DocTypeTreeResource;

class DocTypeController extends Controller
{
    public function __construct(private DocTypeInterface $modelInterface)
    {
        $this->modelInterface = $modelInterface;
    }

    public function all(Request $request)
    {

        $models = $this->modelInterface->all($request);
        return responseJson(200, 'success', AllDocTypeResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function find($id)
    {
        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, 'data not found');
        }
        return responseJson(200, 'success', new AllDocTypeResource($model));
    }

    public function create(DocTypeRequest $request)
    {
        $model = $this->modelInterface->create($request);
        $model->refresh();
        return responseJson(200, 'success', new AllDocTypeResource($model));

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

    public function update(DocTypeRequest $request, $id)
    {
        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, 'data not found');
        }
        $this->modelInterface->update($request, $id);
        $model->refresh();
        return responseJson(200, 'success', new AllDocTypeResource($model));
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

    // tree of doc types
    public function tree()
    {
        $models = $this->modelInterface->tree();
        return responseJson(200, 'success', DocTypeTreeResource::collection($models));
    }
    public function nodesLevelTwo(Request $request)
    {
        $models = $this->modelInterface->nodesLevelTwo($request);
        return responseJson(200, 'success', \Modules\Archiving\Transformers\DocRelationResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }
    public function addStatusToDocumentType(Request $request)
    {
        if ($this->modelInterface->documentStatusExist($request->status_id, $request->doc_type_id)) {
            return response()->json(["error" => "Document status exist"], 422);
        }
        $this->modelInterface->addStatusToDocumentType($request);
    }

    public function removeStatusFromDocumentType($doc_type_id, $status_id)
    {
        $this->modelInterface->removeStatusFromDocumentType($doc_type_id, $status_id);
    }

    public function getDocTypeStatuses($doc_type_id)
    {
        return DocType::find($doc_type_id)->statuses;
    }
}
