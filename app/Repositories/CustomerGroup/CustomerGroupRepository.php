<?php

namespace App\Repositories\CustomerGroup;

use App\Models\CustomerGroup;
use Illuminate\Support\Facades\DB;

class CustomerGroupRepository implements CustomerGroupInterface
{

    public function __construct(private CustomerGroup $model)
    {
        $this->model = $model;
    }

    public function all($request)
    {
        $models = $this->model->data()->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

        if ($request->per_page) {
            return ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            return ['data' => $models->get(), 'paginate' => false];
        }
    }

    public function find($id)
    {
        $data = $this->model->data()->find($id);
        return $data;
    }

    public function create($request)
    {
        checkIsDefaultGeneral($request['is_default'], $this->model);

        return DB::transaction(function () use ($request) {
            return $this->model->create($request);
        });
    }

    public function update($request, $id)
    {
        checkIsDefaultGeneral($request['is_default'], $this->model);

        $data = $this->model->find($id);
        $data->update($request);
        return $data;
    }

    public function logs($id)
    {
        return $this->model->find($id)->activities()->orderBy('created_at', 'DESC')->get();
    }

    public function delete($id)
    {
        $model = $this->model->find($id);
        $model->delete();
    }

    public function getName($request)
    {
        $models = $this->model->select('id', 'title', 'title_e');

        if ($request->per_page) {
            return ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            return ['data' => $models->get(), 'paginate' => false];
        }
    }
}
