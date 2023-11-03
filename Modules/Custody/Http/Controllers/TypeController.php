<?php

namespace Modules\Custody\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Custody\Entities\Type;
use Modules\Custody\Http\Requests\TypeRequest;
use Modules\Custody\Transformers\TypeResource;

class TypeController extends Controller
{
    public function __construct(private Type $model)
    {
        $this->model = $model;
    }
    public function find($id)
    {
        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }
        return responseJson(200, 'success', new TypeResource($model));
    }

    public function all(Request $request)
    {
        $models = $this->model->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

        if ($request->per_page) {
            $models = ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            $models = ['data' => $models->get(), 'paginate' => false];
        }

        return responseJson(200, 'success', TypeResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function create(TypeRequest $request)
    {
        $model = $this->model->create($request->validated());
        $model->refresh();

        return responseJson(200, 'created', new TypeResource($model));
    }

    public function update($id, TypeRequest $request)
    {
        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }

        $model->update($request->validated());

        $model->refresh();
        return responseJson(200, 'updated', new TypeResource($model));
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
        if ($model->children()) {
            return responseJson(400, 'this data has children');
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
        if (count($ids) != $models->count()) {
            return responseJson(400, 'some ids are not found');
        }
        foreach ($models as $model) {
            if ($model->children()) {
                return responseJson(400, 'some data have children');
            }
        }
        $this->model->whereIn('id', $ids)->delete();
        return responseJson(200, 'deleted');
    }
}
