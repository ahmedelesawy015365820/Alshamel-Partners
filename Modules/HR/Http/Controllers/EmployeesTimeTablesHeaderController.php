<?php

namespace Modules\HR\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\HR\Entities\EmployeesTimeTablesDetail;
use Modules\HR\Entities\EmployeesTimeTablesHeader;
use Modules\HR\Http\Requests\EmployeesTimeTablesHeaderRequest;
use Modules\HR\Transformers\EmployeesTimeTablesHeaderResource;

class EmployeesTimeTablesHeaderController extends Controller
{
    public function __construct(private EmployeesTimeTablesHeader $model, private EmployeesTimeTablesDetail $employeeModelTimeDetail)
    {
        $this->model = $model;
        $this->employeeModelTimeDetail = $employeeModelTimeDetail;
    }

    public function all(Request $request)
    {
        $models = $this->model->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

        if ($request->per_page) {
            $models = ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            $models = ['data' => $models->get(), 'paginate' => false];
        }

        return responseJson(200, 'success', EmployeesTimeTablesHeaderResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function find($id)
    {
        $model = $this->model->data()->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        return responseJson(200, 'success', new EmployeesTimeTablesHeaderResource($model));
    }

    public function create(EmployeesTimeTablesHeaderRequest $request)
    {
        $model = $this->model->create($request->validated());
        return responseJson(200, 'success',new EmployeesTimeTablesHeaderResource($model));
    }

    public function update($id, EmployeesTimeTablesHeaderRequest $request)
    {
        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }

        $model->update($request->validated());

        if ($request->employee_time_details) {

            $employeeModelTimeDetails = $this->employeeModelTimeDetail->where('employees_timetables_header_id', $model->id)->get();
            if ($employeeModelTimeDetails) {

                foreach ($employeeModelTimeDetails as $employeeModelTimeDetail) {
                    $employeeModelTimeDetail->delete();
                }
            }
            foreach ($request->employee_time_details as $value) {
                $this->employeeModelTimeDetail->create([
                    'employees_timetables_header_id' => $id,
                    'employee_id' => $value,

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

    public function logs($id)
    {
        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }

        $logs = $model->activities()->orderBy('created_at', 'DESC')->get();
        return responseJson(200, 'success', \App\Http\Resources\Log\LogResource::collection($logs));
    }


    private function getRelationDisplayName($relation)
    {
        $displayableName = str_replace('_', ' ', $relation);
        return ucwords($displayableName);
    }

}
