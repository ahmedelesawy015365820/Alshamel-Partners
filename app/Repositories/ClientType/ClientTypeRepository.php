<?php

namespace App\Repositories\ClientType;

use App\Models\City;
use App\Models\ClientType;
use Illuminate\Support\Facades\DB;

class ClientTypeRepository implements ClientTypeRepositoryInterface
{
    public $model;
    public function __construct(ClientType $model)
    {
        $this->model = $model;
    }
    public function getAll($request)
    {
        $models = $this->model->data()->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');
        if ($request->per_page) {
            return ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            return ['data' => $models->get(), 'paginate' => false];
        }
    }

    public function create($data)
    {
        return DB::transaction(function () use ($data) {
            return $this->model->create($data);
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
        });
    }

    public function logs($id)
    {
        return $this->model->find($id)->activities()->orderBy('created_at', 'DESC')->get();
    }

    public function delete($id)
    {
        $model = $this->find($id);
        if ($model) {
            // $this->forget($id);
            $model->delete();
        }
    }

    public function getName($request)
    {
        $models = $this->model->select('id', 'name', 'name_e','db_table');
        if ($request->per_page) {
            return ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            return ['data' => $models->get(), 'paginate' => false];
        }
    }
}
