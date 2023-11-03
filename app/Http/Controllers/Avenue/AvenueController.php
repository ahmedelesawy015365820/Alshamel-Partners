<?php

namespace App\Http\Controllers\Avenue;

use App\Http\Requests\AvenueRequest;
use App\Http\Resources\AllDropListResource;
use App\Http\Resources\Avenue\AvenueResource;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class AvenueController extends Controller
{
    public function __construct(private \App\Repositories\Avenue\AvenueInterface $modelInterface)
    {}

    public function find($id)
    {

        $model = $this->modelInterface->find($id);

        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        return responseJson(200, 'success', new AvenueResource($model));
    }

    public function all(Request $request)
    {

        $models = $this->modelInterface->all($request);
        return responseJson(200, 'success', AvenueResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function create(AvenueRequest $request)
    {
        $model = $this->modelInterface->create($request);
        return responseJson(200, 'success', new AvenueResource($model));
    }

    public function update(AvenueRequest $request, $id)
    {

        $model = $this->modelInterface->update($request, $id);

        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }

        return responseJson(200, 'success', new AvenueResource($model));
    }

    public function delete($id)
    {
        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }

        $relationsWithChildren = $model->hasChildren();

        if (!empty($relationsWithChildren)) {
            $errorMessages = [];
            foreach ($relationsWithChildren as $relation) {
                $relationName = $this->getRelationDisplayName($relation['relation']);
                $childCount = $relation['count'];
                $childIds = implode(', ', $relation['ids']);
                $errorMessages[] = "This item has {$childCount} {$relationName} (IDs: {$childIds}) and can't be deleted. Remove its {$relationName} first.";
            }
            return responseJson(400, $errorMessages);
        }

        $this->modelInterface->delete($id);

        return responseJson(200, 'success');
    }

    public function bulkDelete(Request $request)
    {
        $itemsWithRelations = [];

        foreach ($request->ids as $id) {
            $model = $this->modelInterface->find($id);

            $relationsWithChildren = $model->hasChildren();
            if (!empty($relationsWithChildren)) {
                $itemsWithRelations[] = [
                    'id' => $id,
                    'relations' => $relationsWithChildren,
                ];
                continue;
            }

            $this->modelInterface->delete($id);
        }

        if (count($itemsWithRelations) > 0) {
            $errorMessages = [];
            foreach ($itemsWithRelations as $item) {
                $itemId = $item['id'];
                $relations = $item['relations'];

                $relationErrorMessages = [];
                foreach ($relations as $relation) {
                    $relationName = $this->getRelationDisplayName($relation['relation']);
                    $childCount = $relation['count'];
                    $childIds = implode(', ', $relation['ids']);
                    $relationErrorMessages[] = "Item with ID {$itemId} has {$childCount} {$relationName} (IDs: {$childIds}) and can't be deleted. Remove its {$relationName} first.";
                }

                $errorMessages[] = implode(' ', $relationErrorMessages);
            }

            return responseJson(400, $errorMessages);
        }

        return responseJson(200, __('Done'));
    }

    private function getRelationDisplayName($relation)
    {
        $displayableName = str_replace('_', ' ', $relation);
        return ucwords($displayableName);
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

    public function getDropDown(Request $request)
    {

        $models = $this->modelInterface->getName($request);
        return responseJson(200, 'success', AllDropListResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

}
