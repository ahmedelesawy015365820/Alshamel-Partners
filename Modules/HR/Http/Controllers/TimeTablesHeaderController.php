<?php

namespace Modules\HR\Http\Controllers;

use App\Http\Resources\AllDropListResource;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\HR\Entities\TimeTablesDetail;
use Modules\HR\Entities\TimeTablesHeader;
use Modules\HR\Http\Requests\TimeTablesHeaderRequest;
use Modules\HR\Transformers\TimeTablesHeaderResource;

class TimeTablesHeaderController extends Controller
{
    public function __construct(private TimeTablesHeader $model, private TimeTablesDetail $modelTimeDetail)
    {
        $this->model = $model;
        $this->modelTimeDetail = $modelTimeDetail;
    }

    public function all(Request $request)
    {
        $models = $this->model->data()->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

        if ($request->per_page) {
            $models = ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            $models = ['data' => $models->get(), 'paginate' => false];
        }

        return responseJson(200, 'success', TimeTablesHeaderResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function find($id)
    {
        $model = $this->model->data()->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        return responseJson(200, 'success', new TimeTablesHeaderResource($model));
    }

    public function create(TimeTablesHeaderRequest $request)
    {
        $model = $this->model->create($request->validated());
        return responseJson(200, 'success', new TimeTablesHeaderResource($model));
    }

    public function update($id, TimeTablesHeaderRequest $request)
    {
        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }

        $model->update($request->validated());

        if ($request->time_details) {

            $modelTimeDetails = $this->modelTimeDetail->where('timetables_header_id', $model->id)->get();
            if ($modelTimeDetails) {

                foreach ($modelTimeDetails as $modelTimeDetail) {
                    $modelTimeDetail->delete();
                }
            }
            foreach ($request->time_details as $value) {
                $this->modelTimeDetail->create([
                    'timetables_header_id' => $value['timetables_header_id'],
                    'day_no' => $value['day_no'],
                    'shift1_from' => $value['shift1_from'],
                    'shift1_to' => $value['shift1_to'],
                    'shift2_from' => $request->tt_type == 2 ? $value['shift2_from'] : null,
                    'shift2_to' => $request->tt_type == 2 ? $value['shift2_to'] : null,
                    'is_off' => $value['is_off'],
                ]);
            }
        } //

        $model->refresh();
        return responseJson(200, 'updated');
    }

    public function delete($id)
    {
        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
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
        return responseJson(200, 'deleted');
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

    public function logs($id)
    {
        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }

        $logs = $model->activities()->orderBy('created_at', 'DESC')->get();
        return responseJson(200, 'success', \App\Http\Resources\Log\LogResource::collection($logs));
    }

    public function getDropDown(Request $request)
    {
        $models = $this->model->select('id', 'name', 'name_e');

        if ($request->per_page) {
            $models = ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            $models = ['data' => $models->get(), 'paginate' => false];
        }

        return responseJson(200, 'success', AllDropListResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }
}
