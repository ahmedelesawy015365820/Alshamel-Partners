<?php

namespace Modules\PointOfSale\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\PointOfSale\Entities\Payment;
use Modules\PointOfSale\Http\Requests\PaymentRequest;
use Modules\PointOfSale\Transformers\PaymentResource;

class PaymentController extends Controller
{
    public function __construct(private Payment $model)
    {
        $this->model = $model;
    }

    public function find($id)
    {
        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }

        return responseJson(200, 'success', new PaymentResource($model));
    }

    public function all(Request $request)
    {
        $models = $this->model->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

        if ($request->per_page) {
            $models = ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            $models = ['data' => $models->get(), 'paginate' => false];
        }

        return responseJson(200, 'success', PaymentResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function create(PaymentRequest $request)
    {
        $model = $this->model->create($request->validated());
        $model->refresh();
        return responseJson(200, 'created', new PaymentResource($model));

    }

    public function update($id, PaymentRequest $request)
    {

        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }

        $model->update($request->validated());
        $model->refresh();

        return responseJson(200, 'updated', new PaymentResource($model));
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
            return responseJson(404, 'not found');
        }
//        if ($model->hasChildren()) {
//            return responseJson(400, 'can not delete this package because it related data');
//        }

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
            if ($model->hasChildren()) {
                return responseJson(400, 'can not delete this package because it related data');
            }
            $model->delete();
        });
        return responseJson(200, 'deleted');
    }
}