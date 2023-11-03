<?php

namespace App\Http\Controllers\DocumentApprovalDetail;

use App\Http\Controllers\Controller;
use App\Http\Requests\DocumentApprovalDetail\DocumentApprovalDetailRequest;
use App\Http\Resources\DocumentApprovalDetail\DocumentApprovalDetailResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DocumentApprovalDetailController extends Controller
{
    public function __construct(private \App\Repositories\DocumentApprovalDetail\DocumentApprovalDetailInterface $modelInterface)
    {
        $this->modelInterface = $modelInterface;
    }

    public function find($id)
    {
        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        return responseJson(200, 'success', new DocumentApprovalDetailResource($model));
    }


    public function all(Request $request)
    {
        $models = $this->modelInterface->all($request);
        return responseJson(200, 'success', DocumentApprovalDetailResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }


    public function create(DocumentApprovalDetailRequest $request)
    {
        $model = $this->modelInterface->create($request->validated());
        return responseJson(200, 'success', new DocumentApprovalDetailResource($model));
    }



    public function update(DocumentApprovalDetailRequest $request, $id)
    {
        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson( 404 , __('message.data not found'));
        }
        $this->modelInterface->update($request->validated(),$id);
        $model->refresh();
        return responseJson(200, 'success', new DocumentApprovalDetailResource($model));

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
        if ($model->hasChildren()){
            return responseJson(400, __("this item has children and can't be deleted remove it's children first"));

        }
        $this->modelInterface->delete($id);
        return responseJson(200, 'success');
    }

    public function bulkDelete(Request $request)
    {

        foreach ($request->ids as $id) {
            $this->modelInterface->find($id);
            $this->modelInterface->delete($id);
        }
        return responseJson(200, __('Done'));
    }
}
