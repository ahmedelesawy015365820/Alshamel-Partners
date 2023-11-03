<?php

namespace App\Repositories\DepertmentTask;

use App\Models\DepertmentTask;
use Illuminate\Support\Facades\DB;

class DepertmentTaskRepository implements DepertmentTaskInterface
{
    public function __construct(private DepertmentTask $model)
    {
        $this->model = $model;
    }

    public function all($request)
    {
        $models = $this->model->data()->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');
        if ($request->department_id) {
            $models->where('department_id', $request->department_id);
        }
        if ($request->depertment) {
            $models->whereHas('depertment');
        }

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
        DB::transaction(function () use ($request) {
            $this->model->create($request);
        });
    }

    public function update($request, $id)
    {
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
        $models = $this->model->select('id', 'name', 'name_e');

        if ($request->department_id) {
            $models->where('department_id', $request->department_id);
        }
        if ($request->depertment) {
            $models->whereHas('depertment');
        }
        
        if ($request->per_page) {
            return ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            return ['data' => $models->get(), 'paginate' => false];
        }
    }
}
