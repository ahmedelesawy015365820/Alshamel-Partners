<?php

namespace Modules\Booking\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Booking\Entities\Floor;
use Modules\Booking\Http\Requests\FloorRequest;
use Modules\Booking\Transformers\FloorResource;

class FloorController extends Controller
{
    public function __construct(private Floor $model)
    {
        $this->model = $model;
    }

    public function find($id)
    {
        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }

        return responseJson(200, 'success', new FloorResource($model));
    }

    public function all(Request $request)
    {
        $models = $this->model->filter($request);

        if ($request->unit_status_id) {
            $models->where('unit_status_id', $request->unit_status_id);
        }

        if ($request->per_page) {
            $models = ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            $models = ['data' => $models->get(), 'paginate' => false];
        }

        return responseJson(200, 'success', FloorResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function create(FloorRequest $request)
    {
        $model = $this->model->create($request->validated());
        $model->refresh();
        return responseJson(200, 'created');

    }

    public function update($id, FloorRequest $request)
    {
        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }

        $model->update($request->validated());
        $model->refresh();

        return responseJson(200, 'updated');
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

    public function delete($id)
    {
        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }

        if ($model->hasChildren()) {
            return responseJson(404, "not delete");
        }



        $model->delete();

        return responseJson(200, 'success');
    }

    public function bulkDelete()
    {

        $ids = request()->ids;
        if (!$ids) {
            return responseJson(400, 'ids is required');
        }
        $models = $this->model->whereIn('id', $ids)->get();


        $models->each(function ($model) {
            if (!$model->hasChildren()) {
                $model->delete();
            }

        });
        return responseJson(200, 'deleted');
    }

}
