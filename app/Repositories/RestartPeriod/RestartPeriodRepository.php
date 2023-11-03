<?php

namespace App\Repositories\RestartPeriod;

use App\Models\RestartPeriod;
use Illuminate\Support\Facades\DB;

class RestartPeriodRepository implements RestartPeriodInterface
{

    public function __construct(private RestartPeriod $model)
    {
        $this->model = $model;
    }

    public function all($request)
    {
        $models = $this->model->data()->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

        $ids = [5, 6];

        if ($request->serial) {
            $models->whereIn('id', $ids);
        }

        if ($request->per_page) {
            return ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            return ['data' => $models->get(), 'paginate' => false];
        }
    }

    public function find($id)
    {
        return $this->model->data()->find($id);
    }

    public function create($request)
    {

        return DB::transaction(function () use ($request) {

            $model = $this->model->create($request->all());

            return $model;
        });
    }

    public function update($request, $id)
    {
        DB::transaction(function () use ($id, $request) {
            $this->model->where("id", $id)->update($request->all());
        });

    }

    public function logs($id)
    {
        return $this->model->find($id)->activities()->orderBy('created_at', 'DESC')->get();
    }
    public function delete($id)
    {
        $model = $this->find($id);
        $model->delete();
    }

    public function getName($request)
    {
        $models = $this->model->select('id', 'name', 'name_e');

        $ids = [5, 6];

        if ($request->serial) {
            $models->whereIn('id', $ids);
        }

        if ($request->per_page) {
            return ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            return ['data' => $models->get(), 'paginate' => false];
        }
    }

}
