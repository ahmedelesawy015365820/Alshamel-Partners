<?php

namespace App\Http\Controllers;

use App\Http\Requests\AllRequest;
use App\Http\Requests\CategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class CategoryController extends Controller
{

    public function __construct(private Category $model)
    {
        $this->model = $model;
    }

    public function find($id)
    {
        $model = $this->model->data()->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }

        return responseJson(200, 'success', new CategoryResource($model));
    }

    public function all(AllRequest $request)
    {
        $models = $this->model->data()->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

        if ($request->per_page) {
            $models = ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            $models = ['data' => $models->get(), 'paginate' => false];
        }

        return responseJson(200, 'success', CategoryResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function create(CategoryRequest $request)
    {
        $model = $this->model->create($request->validated());
        $model->refresh();

        return responseJson(200, 'created');

    }

    public function update($id, CategoryRequest $request)
    {
        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }

        $model->update($request->validated());
        $model->refresh();

        return responseJson(200, 'updated');
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
        if (!$model) {
            return responseJson(404, 'not found');
        }

        if ($model->haveChildren) {
            return responseJson(400, __('message.parent have children'));
        }
        $model->delete();

        return responseJson(200, 'deleted');
    }

    public function bulkDelete(Request $request)
    {
        foreach ($request->ids as $id) {
            $model = $this->model->find($id);
            $arr = [];
            if ($model->have_children) {
                $arr[] = $id;
                continue;
            }
            $this->model->delete($id);
        }
        if (count($arr) > 0) {
            return responseJson(400, __('some items has relation cant delete'));
        }
        return responseJson(200, __('Done'));
    }

    // public function delete($id)
    // {
    //     $model = $this->model->find($id);
    //     if (!$model) {
    //         return responseJson(404, __('message.data not found'));
    //     }

    //     $relationsWithChildren = $model->hasChildren();

    //     if (!empty($relationsWithChildren)) {
    //         $errorMessages = [];
    //         foreach ($relationsWithChildren as $relation) {
    //             $relationName = $this->getRelationDisplayName($relation['relation']);
    //             $childCount = $relation['count'];
    //             $childIds = implode(', ', $relation['ids']);
    //             $errorMessages[] = "This item has {$childCount} {$relationName} (IDs: {$childIds}) and can't be deleted. Remove its {$relationName} first.";
    //         }
    //         return responseJson(400, $errorMessages);
    //     }

    //     $this->model->delete($id);

    //     return responseJson(200, 'success');
    // }

    // public function bulkDelete(Request $request)
    // {
    //     $itemsWithRelations = [];

    //     foreach ($request->ids as $id) {
    //         $model = $this->model->find($id);

    //         $relationsWithChildren = $model->hasChildren();
    //         if (!empty($relationsWithChildren)) {
    //             $itemsWithRelations[] = [
    //                 'id' => $id,
    //                 'relations' => $relationsWithChildren,
    //             ];
    //             continue;
    //         }

    //         $this->model->delete($id);
    //     }

    //     if (count($itemsWithRelations) > 0) {
    //         $errorMessages = [];
    //         foreach ($itemsWithRelations as $item) {
    //             $itemId = $item['id'];
    //             $relations = $item['relations'];

    //             $relationErrorMessages = [];
    //             foreach ($relations as $relation) {
    //                 $relationName = $this->getRelationDisplayName($relation['relation']);
    //                 $childCount = $relation['count'];
    //                 $childIds = implode(', ', $relation['ids']);
    //                 $relationErrorMessages[] = "Item with ID {$itemId} has {$childCount} {$relationName} (IDs: {$childIds}) and can't be deleted. Remove its {$relationName} first.";
    //             }

    //             $errorMessages[] = implode(' ', $relationErrorMessages);
    //         }

    //         return responseJson(400, $errorMessages);
    //     }

    //     return responseJson(200, __('Done'));
    // }

    // private function getRelationDisplayName($relation)
    // {
    //     $displayableName = str_replace('_', ' ', $relation);
    //     return ucwords($displayableName);
    // }
}
