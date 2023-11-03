<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AllRequest;
use App\Http\Requests\RoleRequest;
use App\Http\Resources\AllDropListResource;
use Illuminate\Routing\Controller;
use Spatie\Permission\Models\Role;
use App\Http\Resources\RoleResource;
use App\Http\Resources\PermissionsResource;

class RoleController extends Controller
{

    public function __construct(private Role $model)
    {
        $this->model = $model;
    }

    public function find($id)
    {
        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }

        return responseJson(200, 'success', new RoleResource($model));
    }

    public function all(Request $request)
    {
        $models = $this->model->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

        if ($request->per_page) {
            $models = ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            $models = ['data' => $models->get(), 'paginate' => false];
        }

        return responseJson(200, 'success', RoleResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function create(RoleRequest $request)
    {
        $model = $this->model->create($request->validated());
        $model->permissions()->sync($request->permissions);
        $model->refresh();
        return responseJson(200, 'created', new RoleResource($model));
    }

    public function update($id, RoleRequest $request)
    {
        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }

        $model->update($request->validated());
        $model->permissions()->sync($request->permissions);


        $model->refresh();

        return responseJson(200, 'updated', new RoleResource($model));
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

    public function permissions(Request $request)
    {
        $permissions = \Spatie\Permission\Models\Permission::where(function ($q) use ($request) {
            if ($request->workflows) {
                $q->whereIn('crud_name',$request->workflows);
            }
        })->get();
        return responseJson(200, 'success', PermissionsResource::collection($permissions));
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
