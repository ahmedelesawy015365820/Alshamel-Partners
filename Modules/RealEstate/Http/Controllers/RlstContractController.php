<?php

namespace Modules\RealEstate\Http\Controllers;

use App\Http\Requests\AllRequest;
use App\Models\Serial;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\RealEstate\Entities\RlstContract;
use Modules\RealEstate\Entities\RlstContractDetail;
use Modules\RealEstate\Entities\RlstOwner;
use Modules\RealEstate\Entities\RlstReservationDetail;
use Modules\RealEstate\Entities\RlstUnit;
use Modules\RealEstate\Http\Requests\RlstContractFilterRequest;
use Modules\RealEstate\Http\Requests\RlstContractRequest;
use Modules\RealEstate\Transformers\RlstContractFilterResource;
use Modules\RealEstate\Transformers\RlstContractResource;
use Modules\RecievablePayable\Entities\RpBreakDown;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class RlstContractController extends Controller
{

    public function __construct(private RlstContract $model, private Media $media, private RpBreakDown $modelBreakDown,
        private RlstOwner $modelOwner) {

        $this->model = $model;
        $this->media = $media;
        $this->modelBreakDown = $modelBreakDown;
        $this->modelOwner = $modelOwner;
    }

    public function generalFilter(RlstContractFilterRequest $request)
    {

        $models = \Modules\RealEstate\Entities\RlstContractDetail::whereHas('contract', function ($q) use ($request) {
            $q->filter($request)->generalfilter($request);
        })->with('contract')->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

        if ($request->per_page) {
            $models = ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            $models = ['data' => $models->get(), 'paginate' => false];
        }
        // return $models;
        return responseJson(200, 'success', RlstContractFilterResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);

    }

    public function find($id)
    {
        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }

        return responseJson(200, 'success', new RlstContractResource($model));
    }

    public function all(AllRequest $request)
    {
        $models = $this->model->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

        if ($request->per_page) {
            $models = ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            $models = ['data' => $models->get(), 'paginate' => false];
        }

        return responseJson(200, 'success', RlstContractResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function create(RlstContractRequest $request)
    {

        $model = DB::transaction(function () use ($request) {

            $data = $request->validated();
            $serial = Serial::where([['branch_id', $request->branch_id], ['document_id', $request->document_id]])->first();

            $data['serial_id'] = $serial ? $serial->id : null;
            $model = $this->model->create($data);
            if ($request->media) {
                foreach ($request->media as $media) {
                    $this->media::where('id', $media)->update([
                        'model_id' => $model->id,
                        'model_type' => get_class($this->model),
                    ]);
                }
            }

            foreach ($request->details as $detail) {
                $model->details()->create($detail);

                RlstUnit::find($detail['unit_id'])->update(['unit_status_id' => 3]);
            }

            $model->refresh();
            $serials = generalSerial($model);
            $model->update([
                "serial_number" => $serials['serial_number'],
                "prefix" => $serials['prefix'],
            ]);
            return $model;

        });

        return responseJson(200, 'created', new RlstContractResource($model));
    }

    public function update($id, RlstContractRequest $request)
    {
        return DB::transaction(function () use ($id, $request) {
            $model = $this->model->find($id);
            if (!$model) {
                return responseJson(404, 'not found');
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

            foreach ($request->details as $detail) {
                if (isset($detail['id'])) {
                    $model->details()->update($detail);
                } else {
                    $model->details()->create($detail);
                }
            }
            $serials = generalSerialUpdate($model);
            $model->update([
                "serial_number" => $serials['serial_number'],
                "prefix" => $serials['prefix'],
            ]);
            $model->refresh();

            return responseJson(200, 'updated', new RlstContractResource($model));
        });

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
    //     if ($model->breakDowns) {
    //         $this->modelBreakDown->where([['break_id', $model->id], ['break_type', 'contract']])->delete();
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

        $this->model->delete($id);

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

            $this->model->delete($id);
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

    public function getSerialNumber($unit_id, $unit_status_id)
    {
        if ($unit_status_id == 3) { //sold

            $contract_detail = RlstContractDetail::where('unit_id', $unit_id)
                ->whereHas('unit', function ($query) use ($unit_status_id) {
                    $query->where('unit_status_id', $unit_status_id);
                })
                ->with('contract')
                ->first();
            if (!$contract_detail) {
                return response()->json(['message' => 'Unit not found in contracts'], 404);
            }

            $serial_number = $contract_detail->contract->serial_number;

        } elseif ($unit_status_id == 2) { //reserved

            $reservation_detail = RlstReservationDetail::where('unit_id', $unit_id)
                ->whereHas('unit', function ($query) use ($unit_status_id) {
                    $query->where('unit_status_id', $unit_status_id);
                })
                ->with('reservation')
                ->first();
            if (!$reservation_detail) {
                return response()->json(['message' => 'Unit not found in reservations'], 404);
            }

            $serial_number = $reservation_detail->reservation->serial_number;
        }

        return response()->json(['serial_number' => $serial_number], 200);
    }
}
