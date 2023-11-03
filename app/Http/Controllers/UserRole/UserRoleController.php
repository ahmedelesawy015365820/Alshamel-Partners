<?php

namespace App\Http\Controllers\UserRole;

use App\Http\Requests\AllRequest;
use App\Http\Requests\UserRole\StoreUserRoleRequest;
use App\Http\Requests\UserRole\UpdateUserRoleRequest;
use App\Http\Resources\UserRole\UserRoleResource;
use App\Models\RoleUser;
use Illuminate\Routing\Controller;

class UserRoleController extends Controller
{

    public function __construct(private RoleUser $model)
    {
        $this->model = $model;
    }

    public function find($id)
    {
        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }

        return responseJson(200, 'success', new UserRoleResource($model));
    }

    public function all(AllRequest $request)
    {
        $models = $this->model->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

        if ($request->per_page) {
            $models = ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            $models = ['data' => $models->get(), 'paginate' => false];
        }

        return responseJson(200, 'success', UserRoleResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function create(StoreUserRoleRequest $request)
    {
        $data = [];
        foreach ($request->users as $user) {
            $model = $this->model->create([
                'user_id' => $user,
                'role_id' => $request->role,
                "company_id" => $request->company_id,
            ]);
            $data[] = new UserRoleResource($model);
        }

        return responseJson(200, 'created', $data);
    }

    public function update($id, UpdateUserRoleRequest $request)
    {
        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }
        $model->update($request->all());
        $model->refresh();
        return responseJson(200, 'updated', new UserRoleResource($model));
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

    public function logs($id)
    {
        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }

        $logs = $model->activities()->orderBy('created_at', 'DESC')->get();
        return responseJson(200, 'success', \App\Http\Resources\Log\LogResource::collection($logs));
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
    public function getRoleUsersCount($roleId)
    {
        return ["user_count" => RoleUser::where("role_id", $roleId)->count()];
    }
}
