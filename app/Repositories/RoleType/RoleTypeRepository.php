<?php

namespace App\Repositories\RoleType;

use App\Models\RoleType;
use Illuminate\Support\Facades\DB;

class RoleTypeRepository implements RoleTypeRepositoryInterface
{
    public $model;
    public function __construct(RoleType $model)
    {
        $this->model = $model;
    }
    public function getAll($request)
    {
        $models = $this->model->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

        if ($request->per_page) {
            return ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            return ['data' => $models->get(), 'paginate' => false];
        }
    }

    public function create(array $data)
    {
        DB::transaction(function () use ($data) {
            $this->model->create(request()->all());
            cacheForget("role_types");
        });
    }

    public function find($id)
    {
        return $this->model->find($id);
    }

    public function update($data, $id)
    {
        DB::transaction(function () use ($id, $data) {
            $this->model->where("id", $id)->update($data);
            $this->forget($id);
        });
    }

    public function delete($id)
    {
        $model = $this->find($id);
        if ($model) {
            $this->forget($id);
            $model->delete();
        }
    }
    public function logs($id)
    {
        return $this->model->find($id)->activities()->orderBy('created_at', 'DESC')->get();
    }
    private function forget($id)
    {
        $keys = [
            "role_types",
            "role_types_" . $id,
        ];
        foreach ($keys as $key) {
            cacheForget($key);
        }

    }
}
