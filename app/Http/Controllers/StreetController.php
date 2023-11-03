<?php

namespace App\Http\Controllers;

use App\Http\Requests\AllRequest;
use App\Http\Requests\StreetRequest;
use App\Http\Resources\AllDropListResource;
use App\Http\Resources\StreetResource;
use App\Models\Street;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class StreetController extends Controller
{

    public function __construct(private Street $model)
    {
        $this->model = $model;
    }

    public function find($id)
    {
        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }

        return responseJson(200, 'success', new StreetResource($model));
    }

    public function all(AllRequest $request)
    {
        $models = $this->model->data()->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

        if ($request->avenue_id) {
            $models->whereAvenueId($request->avenue_id);
        }

        if ($request->per_page) {
            $models = ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            $models = ['data' => $models->get(), 'paginate' => false];
        }

        return responseJson(200, 'success', StreetResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function create(StreetRequest $request)
    {
        $model = $this->model->create($request->validated());
        $model->refresh();

        return responseJson(200, 'created', new StreetResource($model));

    }

    public function update($id, StreetRequest $request)
    {
        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }

        $model->update($request->validated());
        $model->refresh();

        return responseJson(200, 'updated', new StreetResource($model));
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

    public function getDropDown(Request $request)
    {

        $models = $this->model->select('id', 'name', 'name_e')->where('is_active', 'active');

        if ($request->avenue_id) {
            $models->where('avenue_id', $request->avenue_id);
        }

        if ($request->per_page) {
            $models = ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            $models = ['data' => $models->get(), 'paginate' => false];
        }

        return responseJson(200, 'success', AllDropListResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);

    }
}
