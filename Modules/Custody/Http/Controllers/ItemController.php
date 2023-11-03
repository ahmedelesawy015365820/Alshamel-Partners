<?php

namespace Modules\Custody\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Custody\Entities\Item;
use Modules\Custody\Http\Requests\ItemRequest;
use Modules\Custody\Transformers\ItemResource;

class ItemController extends Controller
{
    public function __construct(private Item $model)
    {
        $this->model = $model;
    }
    public function find($id)
    {
        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }
        return responseJson(200, 'success', new ItemResource($model));
    }

    public function all(Request $request)
    {
        $models = $this->model->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

        if ($request->per_page) {
            $models = ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            $models = ['data' => $models->get(), 'paginate' => false];
        }

        return responseJson(200, 'success', ItemResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function create(ItemRequest $request)
    {
        return DB::transaction(function () use ($request) {
            $model = $this->model->create($request->all());
            $model->refresh();
            $model->types()->attach($request->types);
            return responseJson(200, 'created', new ItemResource($model));

        });

    }

    public function update($id, ItemRequest $request)
    {
        return DB::transaction(function () use ($request, $id) {
            $model = $this->model->find($id);
            if (!$model) {
                return responseJson(404, 'not found');
            }
            $model->update($request->all());
            $model->refresh();
            $model->types()->sync($request->types);
            return responseJson(200, 'updated', new ItemResource($model));
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
