<?php

namespace Modules\Archiving\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Archiving\Entities\DocType;
use Modules\Archiving\Entities\DocTypeField;
use Modules\Archiving\Http\Requests\DocTypeFieldRequest;
use Modules\Archiving\Repositories\DocTypeFieldInterface;
use Modules\Archiving\Transformers\DocTypeFieldResource;

class DocTypeFieldController extends Controller
{
    public function __construct(private DocTypeFieldInterface $modelInterface)
    {
        $this->modelInterface = $modelInterface;
    }

    public function all(Request $request)
    {

        $models = $this->modelInterface->all($request);
        return responseJson(200, 'success', DocTypeFieldResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function create(DocTypeFieldRequest $request)
    {
        $model = $this->modelInterface->create($request);
        return responseJson(200, 'success', new DocTypeFieldResource($model));
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

    public function bulkInsert(Request $request)
    {
        DocTypeField::query()->insert($request->all());
        return responseJson(200, __('Done'));
    }

    public function bulkUpdate(Request $request)
    {
        $parentChildren = DocTypeField::where('parent_id', $request->all()[0]['doc_type_id'])->get();
        $nonRepeatParentChild = [];
        foreach ($parentChildren as $child) {
            if (!in_array($child->doc_type_id, $nonRepeatParentChild))
                $nonRepeatParentChild[] = $child->doc_type_id;
        }
        foreach ($request->all() as $item) {
            DocTypeField::query()->where('doc_type_id', $item['doc_type_id'])->forceDelete();
            DocTypeField::where("parent_id", $item['doc_type_id'])->forceDelete();
        }
        DocTypeField::query()->insert($request->all());
        if (count($nonRepeatParentChild) > 0) {
            foreach ($nonRepeatParentChild as $child) {
                foreach ($request->all() as $item) {
                    DocTypeField::query()->insert([
                        "doc_type_id"=>$child,
                        "doc_field_id"=>$item["doc_field_id"],
                        "field_order"=>$item["field_order"],
                        "is_required"=>$item["is_required"],
                        "parent_id"=>$item["doc_type_id"]
                    ]);
                }
            }
        }
        return responseJson(200, __('Done'));
    }

    public function update(DocTypeFieldRequest $request, $id)
    {
        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, 'data not found');
        }
        $this->modelInterface->update($request, $id);
        $model->refresh();
        return responseJson(200, 'success', new DocTypeFieldResource($model));
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

    public function idDocTypeField($id){

        $model = $this->modelInterface->idDocTypeField($id);

        return responseJson(200, 'success', DocTypeFieldResource::collection($model));
    }
}
