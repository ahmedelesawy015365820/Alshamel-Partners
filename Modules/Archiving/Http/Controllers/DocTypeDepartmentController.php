<?php

namespace Modules\Archiving\Http\Controllers;

use App\Http\Requests\AllRequest;
use Illuminate\Routing\Controller;
use Modules\Archiving\Entities\DocTypeDepartment;
use Modules\Archiving\Http\Requests\DocTypeDepartmentRequest;
use Modules\Archiving\Transformers\DocTypeDepartmentResource;

class DocTypeDepartmentController extends Controller
{

    public function __construct(private DocTypeDepartment $model)
    {
        $this->model = $model;
    }

    public function find($id)
    {
        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }

        return responseJson(200, 'success', new DocTypeDepartmentResource($model));
    }

    public function all(AllRequest $request)
    {
        $models = $this->model->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

        if ($request->per_page) {
            $models = ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            $models = ['data' => $models->get(), 'paginate' => false];
        }

        return responseJson(200, 'success', DocTypeDepartmentResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function create(DocTypeDepartmentRequest $request)
    {
        $model = $this->model->create($request->validated());
        $children=DocTypeDepartment::where("parent_id",$model->arch_doc_type_id)->get();
        foreach($children as $child){
            DocTypeDepartment::insert([
                "arch_doc_type_id"=>$child->arch_doc_type_id,
                "arch_department_id"=>$request->arch_department_id,
                "parent_id"=>$child->parent_id,
            ]);
        }
        $model->refresh();
        return responseJson(200, 'created', new DocTypeDepartmentResource($model));
    }

    public function update($id, DocTypeDepartmentRequest $request)
    {
        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }

        $model->update($request->validated());
        $model->refresh();
        return responseJson(200, 'updated', new DocTypeDepartmentResource($model));
    }

    public function logs($id)
    {
        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }

        $logs = $model->activities()->orderBy('created_at', 'DESC')->get();
        return responseJson(200, 'success', \App\Http\Resources\Log\LogResource::collection($logs));
    }

    public function delete($id)
    {
        $model = $this->model->find($id);
        DocTypeDepartment::where("parent_id",$model->arch_doc_type_id)->delete();
        if (!$model) {
            return responseJson(404, 'not found');
        }
        $model->delete();
        return responseJson(200, 'deleted');
    }

    public function bulkDelete()
    {

        $ids = request()->ids;
        if (!$ids) {
            return responseJson(400, 'ids is required');
        }
        $models = $this->model->whereIn('id', $ids)->get();
        if ($models->count() != count($ids)) {
            return responseJson(404, 'not found');
        }
        $models->each(function ($model) {
            $model->delete();
        });
        return responseJson(200, 'deleted');
    }

    public function getDepartmentByDocument($id)
    {
        $arch_doc_type = \Modules\Archiving\Entities\DocType::find($id);
        if (!$arch_doc_type) {
            return responseJson(404, 'not found');
        }
        $parent = $arch_doc_type->parent_id;

        $models = $this->model->where('arch_doc_type_id', $id)->with('department', 'docType')->get();
        if (!$models) {
            return responseJson(404, 'not found');
        }
        // merge attribute to models
        $models->each(function ($model) use ($parent) {
            $can_edit = $model->parent === $model->docType->parent_id;
            $model->can_edit = $can_edit;

        });

        return responseJson(200, 'success', $models);

    }

}
