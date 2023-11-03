<?php

namespace Modules\RealEstate\Http\Controllers;

use App\Http\Requests\AllRequest;
use Illuminate\Routing\Controller;
use Modules\RealEstate\Entities\RlstCustomer;
use Modules\RealEstate\Http\Requests\RlstCustomerRequest;
use Modules\RealEstate\Transformers\RlstCustomerResource;
use Modules\RealEstate\Http\Requests\CreateRlstCustomerRequest;
use Modules\RealEstate\Http\Requests\UpdateRlstCustomerRequest;

class RlstCustomerController extends Controller
{

    public function __construct(private RlstCustomer $model)
    {
        $this->model = $model;
    }

    public function find($id)
    {
        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }

        return responseJson(200, 'success', new RlstCustomerResource($model));
    }

    public function all(AllRequest $request)
    {
        $models = $this->model->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

        if ($request->per_page) {
            $models = ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            $models = ['data' => $models->get(), 'paginate' => false];
        }

        return responseJson(200, 'success', RlstCustomerResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function create(RlstCustomerRequest $request)
    {
        $model = $this->model->create($request->validated());
        $model->refresh();

        return responseJson(200, 'created', new RlstCustomerResource($model));
    }

    public function update($id, RlstCustomerRequest $request)
    {
        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }

        $model->update($request->validated());
        $model->refresh();
        return responseJson(200, 'updated', new RlstCustomerResource($model));
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
        if ($model->reservations()->count() > 0) {
            return responseJson(400, 'can not delete this customer because it has reservations');
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
        if ($models->count() == 0) {
            return responseJson(404, 'not found');
        }
        foreach ($models as $model) {
            if ($model->reservations()->count() > 0) {
                return responseJson(400, 'can not delete this customer because it has reservations');
            }
        }
        $this->model->whereIn('id', $ids)->delete();
        return responseJson(200, 'deleted');
    }

}
