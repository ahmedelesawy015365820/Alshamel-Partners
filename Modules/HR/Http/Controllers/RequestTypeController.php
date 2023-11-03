<?php

namespace Modules\HR\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\HR\Entities\RequestType;
use Modules\HR\Entities\RequestTypeEmployee;
use Modules\HR\Http\Requests\RequestTypeRequest;
use Modules\HR\Transformers\RequestTypeResource;

class RequestTypeController extends Controller
{
    public function __construct(private RequestType $model, private RequestTypeEmployee $requestTypeEmployee)
    {
        $this->model = $model;
        $this->requestTypeEmployee = $requestTypeEmployee;
    }

    public function all(Request $request)
    {
        $models = $this->model->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

        if ($request->per_page) {
            $models = ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            $models = ['data' => $models->get(), 'paginate' => false];
        }

        return responseJson(200, 'success', RequestTypeResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function create(RequestTypeRequest $request)
    {
        $model = $this->model->create($request->validated());
        if ($model) {
            if ($request['managers']) {
                foreach ($request['managers'] as $employee) {
                    $this->requestTypeEmployee->create([
                        'request_type_id' => $model->id,
                        'employee_id' => $employee,
                    ]);

                }
            }
        }
        return responseJson(200, 'success', new RequestTypeResource($model));

    }

    public function update($id, RequestTypeRequest $request)
    {
        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }

        $model->update($request->validated());
        $model->refresh();

        if ($model) {
            $this->requestTypeEmployee->where('request_type_id', $model->id)->delete();

            if ($request['managers']) {
                foreach ($request['managers'] as $employee) {
                    $this->requestTypeEmployee->create([
                        'request_type_id' => $model->id,
                        'employee_id' => $employee,
                    ]);
                }
            }
        }

        return responseJson(200, 'updated', new RequestTypeResource($model));
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

    public function logs($id)
    {
        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }

        $logs = $model->activities()->orderBy('created_at', 'DESC')->get();
        return responseJson(200, 'success', \App\Http\Resources\Log\LogResource::collection($logs));
    }
}
