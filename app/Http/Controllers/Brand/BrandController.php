<?php

namespace App\Http\Controllers\Brand;

use App\Http\Controllers\Controller;
use App\Http\Requests\Brand\BrandRequest;
use App\Http\Resources\Brand\BrandResource;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function __construct(private \App\Repositories\Brand\BrandInterface $modelInterface)
    {
        $this->modelInterface = $modelInterface;
    }

    public function find($id)
    {
        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        return responseJson(200, 'success', new BrandResource($model));
    }

    public function all(Request $request)
    {
         $models = $this->modelInterface->all($request);
        return responseJson(200, 'success', BrandResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function create(BrandRequest $request)
    {
        $model = $this->modelInterface->create($request->validated());

        return responseJson(200, 'success');
    }

    public function update(BrandRequest $request, $id)
    {
        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        $this->modelInterface->update($request->validated(), $id);
        $model->refresh();
        return responseJson(200, 'success');
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

//     public function delete($id)
//     {
//         $model = $this->modelInterface->find($id);
//         if (!$model) {
//             return responseJson(404, __('message.data not found'));
//         }
// //        if ($model->hasChildren()) {
// //            return responseJson(400, __("this item has children and can't be deleted remove it's children first"));
// //        }
//         $this->modelInterface->delete($id);
//         return responseJson(200, 'success');
//     }

//     public function bulkDelete(Request $request)
//     {

//         foreach ($request->ids as $id) {
//             $model = $this->modelInterface->find($id);
//             $arr = [];
//             if ($model->hasChildren()) {
//                 $arr[] = $id;
//                 continue;
//             }
//             $this->modelInterface->delete($id);
//         }
//         if (count($arr) > 0) {
//             return responseJson(400, __('some items has relation cant delete'));
//         }
//         return responseJson(200, __('Done'));
//     }
}
