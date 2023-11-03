<?php

namespace Modules\RealEstate\Http\Controllers;

use App\Http\Requests\AllRequest;
use App\Http\Resources\AllDropListResource;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\RealEstate\Entities\RlstUnit;
use Modules\RealEstate\Entities\RlstWallet;
use Modules\RealEstate\Http\Requests\RlstUnitEditRequest;
use Modules\RealEstate\Http\Requests\RlstUnitFilterRequest;
use Modules\RealEstate\Http\Requests\RlstUnitRequest;
use Modules\RealEstate\Transformers\RlstUnitFilterResource;
use Modules\RealEstate\Transformers\RlstUnitResource;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class RlstUnitController extends Controller
{

    public function __construct(private RlstUnit $model, private RlstWallet $modelWallet, private \Spatie\MediaLibrary\MediaCollections\Models\Media $media)
    {
        $this->model = $model;
        $this->modelWallet = $modelWallet;
        $this->media = $media;
    }

    public function generalFilter(RlstUnitFilterRequest $request)
    {


        $models = $this->model->data()->filter($request)->generalfilter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

        if ($request->per_page) {
            $models = ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            $models = ['data' => $models->get(), 'paginate' => false];
        }
        // return $models;
        return responseJson(200, 'success', RlstUnitFilterResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);

    }

    public function find($id)
    {
        $model = $this->model->data()->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }

        return responseJson(200, 'success', new RlstUnitResource($model));
    }

    public function all(AllRequest $request)
    {

        $models = $this->model->data()->when($request->building_id, function ($q) use ($request) {
            $q->where("building_id", $request->building_id);
        })
            ->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

        if ($request->unit_status_id) {
            $models->where('unit_status_id', $request->unit_status_id);
        }

        if ($request->per_page) {
            $models = ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            $models = ['data' => $models->get(), 'paginate' => false];
        }

        return responseJson(200, 'success', RlstUnitResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function create(RlstUnitRequest $request)
    {
        $model = $this->model->create($request->validated());
        $model->refresh();

        if ($request->video_link) {
            $model->video()->create([
                'link' => $request->video_link,
            ]);
        }
        if ($request->media) {
            foreach ($request->media as $media) {
                $this->media::where('id', $media)->update([
                    'model_id' => $model->id,
                    'model_type' => get_class($this->model),
                ]);
            }
        }
        return responseJson(200, 'created', new RlstUnitResource($model));

    }

    public function update($id, RlstUnitEditRequest $request)
    {
        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }

        $model->update($request->all());
        $model->refresh();
        if ($request->video_link) {
            $model->video()->delete();
            $model->video()->create([
                'link' => $request->video_link,
            ]);
        }
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

        return responseJson(200, 'updated', new RlstUnitResource($model));
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

    // public function delete($id)
    // {
    //     $model = $this->model->find($id);
    //     if (!$model) {
    //         return responseJson(404, 'not found');
    //     }

    //     if ($model->hasChildren()) {
    //         return responseJson(400, __("this item has children and can't be deleted remove it's children first"));
    //     }
    //     $model->delete();
    //     return responseJson(200, 'deleted');
    // }

    // public function bulkDelete(Request $request)
    // {
    //     foreach ($request->ids as $id) {
    //         $model = $this->model->find($id);
    //         $arr = [];
    //         if ($model->hasChildren()) {
    //             $arr[] = $id;
    //             continue;
    //         }
    //         $this->model->delete($id);
    //     }
    //     if (count($arr) > 0) {
    //         return responseJson(400, __('some items has relation can\'t delete'));
    //     }
    //     return responseJson(200, __('Done'));
    // }

    public function delete($id)
    {
        $model = $this->model->find($id);
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

        $model->delete();

        return responseJson(200, 'success');
    }

    public function bulkDelete(Request $request)
    {
        $itemsWithRelations = [];

        foreach ($request->ids as $id) {
            $model = $this->model->find($id);

            $relationsWithChildren = $model->hasChildren();
            if (!empty($relationsWithChildren)) {
                $itemsWithRelations[] = [
                    'id' => $id,
                    'relations' => $relationsWithChildren,
                ];
                continue;
            }

            $model->delete();
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

    public function getOwnerByWalletId($wallet_id)
    {
        $owner = $this->modelWallet->with('owners')->find($wallet_id);
        // $owner = $wallet->owners;
        return responseJson(200, 'success', $owner);
    }

    public function getBuildingByWalletId(Request $request)
    {
        $building =
            $this->modelWallet
            ->with('buildings:id,name,name_e,country_id,city_id','owners:id,name,name_e')
            ->whereIn('id',$request->wallet_ids)->get();
        // $owner = $wallet->owners;
        return responseJson(200, 'success', $building);
    }


    public function getDropDown(AllRequest $request)
    {
        $models = $this->model->select('id','name','name_e')->when($request->building_id, function ($q) use ($request) {
            $q->where("building_id", $request->building_id);
        })
            ->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

        if ($request->unit_status_id) {
            $models->where('unit_status_id', $request->unit_status_id);
        }

        if ($request->per_page) {
            $models = ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            $models = ['data' => $models->get(), 'paginate' => false];
        }

        return responseJson(200, 'success', AllDropListResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }


}
