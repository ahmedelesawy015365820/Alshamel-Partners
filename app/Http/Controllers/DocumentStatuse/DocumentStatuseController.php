<?php

namespace App\Http\Controllers\DocumentStatuse;

use App\Http\Controllers\Controller;
use App\Http\Requests\DepertmentRequest;
use App\Http\Requests\DocumentStatuse\DocumentStatuseRequest;
use App\Http\Resources\Depertment\DepertmentResource;
use App\Http\Resources\DocumentStatuse\DocumentStatuseResource;
use App\Repositories\DocumentStatuse\DocumentStatuseRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DocumentStatuseController extends Controller
{
    public function __construct(private \App\Repositories\DocumentStatuse\DocumentStatuseInterface $modelInterface)
    {
        $this->modelInterface = $modelInterface;
    }

    public function find($id)
    {
        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        return responseJson(200, 'success', new DocumentStatuseResource($model));
    }


    public function all(Request $request)
    {
        $models = $this->modelInterface->all($request);
        return responseJson(200, 'success', DocumentStatuseResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }


    public function create(DocumentStatuseRequest $request)
    {
        $model = $this->modelInterface->create($request->validated());

        return responseJson(200, 'success', new DocumentStatuseResource($model));
    }



    public function update(DocumentStatuseRequest $request, $id)
    {
        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson( 404 , __('message.data not found'));
        }
        $this->modelInterface->update($request->validated(),$id);
        $model->refresh();
        return responseJson(200, 'success', new DocumentStatuseResource($model));

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
            // $model = $this->modelInterface->find($id);
            // $arr = [];
            // if ($model->hasChildren()) {
            //     $arr[] = $id;
            //     continue;
            // }
            $this->modelInterface->delete($id);
        }
        // if (count($arr) > 0) {
        //     return responseJson(400, __('some items has relation cant delete'));
        // }
        return responseJson(200, __('Done'));
    }
}
