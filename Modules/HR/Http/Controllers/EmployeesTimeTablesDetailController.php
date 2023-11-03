<?php

namespace Modules\HR\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\HR\Entities\EmployeesTimeTablesDetail;
use Modules\HR\Http\Requests\EmployeesTimeTablesDetailRequest;
use Modules\HR\Transformers\EmployeesTimeTablesDetailResource;
use Modules\HR\Transformers\GetEmployeesTimeTablesDetailsResource;

class EmployeesTimeTablesDetailController extends Controller
{
    public function __construct(private EmployeesTimeTablesDetail $model, private Employee $modelEmployee)
    {
        $this->model = $model;
        $this->modelEmployee = $modelEmployee;
    }

    public function all(Request $request)
    {
        $models = $this->model->data()->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

        if ($request->per_page) {
            $models = ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            $models = ['data' => $models->get(), 'paginate' => false];
        }

        return responseJson(200, 'success', EmployeesTimeTablesDetailResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function find($id)
    {
        $model = $this->model->data()->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        return responseJson(200, 'success', new EmployeesTimeTablesDetailResource($model));
    }

    public function create(EmployeesTimeTablesDetailRequest $request)
    {

        if ($request->employee_time_details) {
            foreach ($request->employee_time_details as $value) {
                $this->model->create([
                    'employees_timetables_header_id' => $request->employees_timetables_header_id,
                    'employee_id' => $value['employee_id'],

                ]);
            }
        }
        return responseJson(200, 'success');

    }

    // public function update($id, EmployeesTimeTablesDetailRequest $request)
    // {
    //     $model = $this->model->find($id);
    //     if (!$model) {
    //         return responseJson(404, 'not found');
    //     }

    //     $validatedData = $request->validated();

    //     // Update the record
    //     $model->update([
    //         'timetables_header_id' => $validatedData['timetables_header_id'],
    //         'day_no' => $validatedData['day_no'],
    //         'shift1_from' => $validatedData['shift1_from'],
    //         'shift1_to' => $validatedData['shift1_to'],
    //         'shift2_from' => $validatedData['shift2_from'],
    //         'shift2_to' => $validatedData['shift2_to'],
    //         'is_off' => $validatedData['is_off'],
    //     ]);

    //     $model->refresh();
    //     return responseJson(200, 'updated');
    // }

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

    public function logs($id)
    {
        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }

        $logs = $model->activities()->orderBy('created_at', 'DESC')->get();
        return responseJson(200, 'success', \App\Http\Resources\Log\LogResource::collection($logs));
    }

    // public function getEmployees(Request $request)
    // {
    //     $models = $this->modelEmployee->EmployeesTimeTablesDetailsData()->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

    //     if ($request->department_id) {
    //         $models->where('department_id', $request->department_id);
    //     }
    //     if ($request->branch_id) {
    //         $models->where('branch_id', $request->branch_id);
    //     }
    //     if ($request->id) {
    //         $models->where('id', $request->id);
    //     }
    //     if ($request->name) {
    //         $models->where('name', $request->name);
    //     }
    //     if ($request->name_e) {
    //         $models->where('name_e', $request->name_e);
    //     }

    //     if ($request->per_page) {
    //         return ['data' => $models->paginate($request->per_page), 'paginate' => true];
    //     } elseif ($request->limet) {
    //         return ['data' => $models->take($request->limet)->get(), 'paginate' => false];
    //     } else {
    //         return ['data' => $models->get(), 'paginate' => false];
    //     }

    //     return responseJson(200, 'success', $models['data'], $models['paginate'] ? getPaginates($models['data']) : null);
    // }

    public function getEmployees(Request $request)
    {
        $models = $this->modelEmployee->EmployeesTimeTablesDetailsData()->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

        if ($request->department_id) {
            $models->where('department_id', $request->department_id);
        }
        if ($request->branch_id) {
            $models->where('branch_id', $request->branch_id);
        }
        if ($request->id) {
            $models->where('id', $request->id);
        }
        if ($request->name) {
            $models->where('name','like' ,'%'.$request->name.'%');
        }
        if ($request->name_e) {
            $models->where('name_e', 'like' ,'%'.$request->name.'%');
        }

        if ($request->per_page) {
            $models = ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } elseif ($request->limet) {
            return ['data' => $models->take($request->limet)->get(), 'paginate' => false];
        } else {
            $models = ['data' => $models->get(), 'paginate' => false];
        }

        return responseJson(200, 'success', GetEmployeesTimeTablesDetailsResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function getEmployeesTimeTablesDetails(Request $request)
    {
        $models = $this->modelEmployee->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

        if ($request->employees_timetables_header_id) {
            $models->whereRelation('EmployeesTimeTablesDetails', 'employees_timetables_header_id', $request->employees_timetables_header_id);
        }

        

        if ($request->per_page) {
            $models = ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } elseif ($request->limet) {
            return ['data' => $models->take($request->limet)->get(), 'paginate' => false];
        } else {
            $models = ['data' => $models->get(), 'paginate' => false];
        }

        return responseJson(200, 'success', GetEmployeesTimeTablesDetailsResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

}
