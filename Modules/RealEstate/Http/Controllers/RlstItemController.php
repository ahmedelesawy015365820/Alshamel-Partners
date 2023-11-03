<?php

namespace Modules\RealEstate\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\RealEstate\Entities\RlstItem;
use Modules\RealEstate\Http\Requests\ItemRequest;
use Modules\RealEstate\Repositories\RlstItem\RlstItemInterface;
use Modules\RealEstate\Transformers\RlstItemResource;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class RlstItemController extends Controller
{

    public function __construct(private RlstItem $model, private RlstItemInterface $modelInterface)
    {
        $this->model = $model;
        $this->modelInterface = $modelInterface;
    }

    public function all(Request $request)
    {
        $models = $this->modelInterface->all($request);

        return responseJson(200, 'success', RlstItemResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function find($id)
    {

        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        return responseJson(200, 'success', new RlstItemResource($model));
    }

    public function create(ItemRequest $request)
    {
        $model = $this->modelInterface->create($request);
        // if ($request->media) {
        //     foreach ($request->media as $media) {
        //         $this->media::where('id', $media)->update([
        //             'model_id' => $model->id,
        //             'model_type' => get_class($this->model),
        //         ]);
        //     }
        // }
        $model->refresh();
        return responseJson(200, 'success', new RlstItemResource($model));
    }

    public function update(ItemRequest $request, $id)
    {

        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }

        $model->update($request->validated());

        if ($request->media && !$request->old_media) { // if there is new media and no old media
            $model->clearMediaCollection('media');
            foreach ($request->media as $media) {
                uploadImage($media, [
                    'model_id' => $model->id,
                    'model_type' => get_class($this->model),
                ]);
            }
        }

        if ($request->old_media && !$request->media) { // if there is old media and no new media
            $model->media->whereNotIn('id', $request->old_media)->each(function (Media $media) {
                $media->delete();
            });
        }

        if ($request->old_media && $request->media) { // if there is old media and new media
            $model->media->whereNotIn('id', $request->old_media)->each(function (Media $media) {
                $media->delete();
            });
            foreach ($request->media as $image) {
                uploadImage($image, [
                    'model_id' => $model->id,
                    'model_type' => get_class($this->model),
                ]);
            }
        }

        if (!$request->old_media && !$request->media) { // if this is no old media and new media
            $model->clearMediaCollection('media');
        }

        $model->refresh();

        return responseJson(200, 'success', new RlstItemResource($model));
    }

    // public function delete($id)
    // {
    //     $model = $this->modelInterface->find($id);
    //     if (!$model) {
    //         return responseJson(404, __('message.data not found'));
    //     }
    //     if ($model->haveChildren) {
    //         return responseJson(400, __('message.parent have children'));
    //     }
    //     $this->modelInterface->delete($id);

    //     return responseJson(200, 'success');
    // }

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

    public function logs($id)
    {
        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        $logs = $this->modelInterface->logs($id);
        return responseJson(200, 'success', $logs);
    }

    // public function bulkDelete(Request $request)
    // {
    //     foreach ($request->ids as $id) {
    //         $model = $this->modelInterface->find($id);
    //         $arr = [];
    //         if ($model->have_children) {
    //             $arr[] = $id;
    //             continue;
    //         }
    //         $this->modelInterface->delete($id);
    //     }
    //     if (count($arr) > 0) {
    //         return responseJson(400, __('some items has relation cant delete'));
    //     }
    //     return responseJson(200, __('Done'));
    // }

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

}
