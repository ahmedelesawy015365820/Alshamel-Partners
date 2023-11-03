<?php

namespace App\Http\Controllers\DocumentModuleType;

use App\Http\Controllers\Controller;
use App\Http\Requests\DocumentModuleType\DocumentModuleTypeRequest;
use App\Http\Resources\DocumentModuleType\DocumentModuleTypeResource;
use App\Http\Resources\DocumentModuleType\ModuleTypeResource;
use App\Repositories\DocumentModuleType\DocumentModuleTypeInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DocumentModuleTypeController extends Controller
{
    public function __construct(DocumentModuleTypeInterface $repository)
    {
        $this->repository = $repository;
    }

    public function find($id)
    {

        $model = $this->repository->find($id);
        if ($model) {
            return responseJson(200, 'success', new DocumentModuleTypeResource($model));
        } else {
            return responseJson(404, __('message.data not found'));
        }

    }

    /**
     * Display a listing of the resource.
     * @return \response
     */
    public function all(Request $request)
    {
        $data = $this->repository->getAll($request);
        return responseJson(200, 'success', DocumentModuleTypeResource::collection($data['data']), $data['paginate'] ? getPaginates($data['data']) : null);
    }

    public function create(DocumentModuleTypeRequest $request)
    {
        $model = $this->repository->create($request->validated());
        return responseJson(200, 'success');
    }

    public function update(DocumentModuleTypeRequest $request, $id)
    {
        $model = $this->repository->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        $model = $this->repository->update($request->validated(), $id);

        return responseJson(200, 'success');
    }


    public function logs($id)
    {
        $model = $this->repository->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        $logs = $this->repository->logs($id);
        return responseJson(200, 'success', \App\Http\Resources\Log\LogResource::collection($logs));
    }

    public function delete($id)
    {
        $model = $this->repository->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }

        $relationsWithChildren = $model->hasChildren();

       if ($relationsWithChildren){
           return responseJson(400, 'can not delete this item because it has children');

       }
        $this->repository->delete($id);

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
        $models = $this->repository->getName($request);
        return responseJson(200, 'success', DocumentModuleTypeResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function getDropDawnModel(Request $request)
    {
        $models = DB::table($request->db_table)->get();
        return responseJson(200, 'success', ModuleTypeResource::collection($models));
    }
}
