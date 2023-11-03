<?php

namespace Modules\HR\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\HR\Entities\TimeTablesDetail;
use Modules\HR\Http\Requests\TimeTablesDetailRequest;
use Modules\HR\Transformers\TimeTablesDetailResource;

class TimeTablesDetailController extends Controller
{
    public function __construct(private TimeTablesDetail $model)
    {
        $this->model = $model;
    }

    public function all(Request $request)
    {
        $models = $this->model->data()->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

        if ($request->per_page) {
            $models = ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            $models = ['data' => $models->get(), 'paginate' => false];
        }

        return responseJson(200, 'success', TimeTablesDetailResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function find($id)
    {
        $model = $this->model->data()->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        return responseJson(200, 'success', new TimeTablesDetailResource($model));
    }

    public function create(TimeTablesDetailRequest $request)
    {

        if ($request->time_details) {
            foreach ($request->time_details as $value) {
                $this->model->create([
                    'timetables_header_id' => $value['timetables_header_id'],
                    'day_no' => $value['day_no'],
                    'shift1_from' => $value['shift1_from'],
                    'shift1_to' => $value['shift1_to'],
                    'shift2_from' => $value['shift2_from'],
                    'shift2_to' => $value['shift2_to'],
                    'is_off' => $value['is_off'],
                ]);
            }
        }

        return responseJson(200, 'success');

    }

    public function update($id, TimeTablesDetailRequest $request)
    {
        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }

        $validatedData = $request->validated();

        // Update the record
        $model->update([
            'timetables_header_id' => $validatedData['timetables_header_id'],
            'day_no' => $validatedData['day_no'],
            'shift1_from' => $validatedData['shift1_from'],
            'shift1_to' => $validatedData['shift1_to'],
            'shift2_from' => $validatedData['shift2_from'],
            'shift2_to' => $validatedData['shift2_to'],
            'is_off' => $validatedData['is_off'],
        ]);

        $model->refresh();
        return responseJson(200, 'updated');
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
