<?php

namespace App\Repositories\SalesmenType;

use Illuminate\Support\Facades\DB;

class SalesmenTypeRepository implements SalesmenTypeInterface
{

    public function __construct(private \App\Models\SalesmenType$model)
    {
        $this->model = $model;

    }

    public function all($request)
    {
        $models = $this->model->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

        if ($request->per_page) {
            return ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            return ['data' => $models->get(), 'paginate' => false];
        }
    }

    public function find($id)
    {
        return $this->model->find($id);
    }

    public function create($request)
    {
        DB::transaction(function () use ($request) {
            $model = $this->model->create($request->all());
            cacheForget("salesmen_types");
        });
    }

    public function update($request, $id)
    {
        DB::transaction(function () use ($id, $request) {
            $model = $this->model->find($id);
            $model->update($request->except(["media"]));

            // $this->forget($id);

        });

    }
    public function logs($id)
    {
        return $this->model->find($id)->activities()->orderBy('created_at', 'DESC')->get();
    }
    public function delete($id)
    {
        $model = $this->find($id);
        // $this->forget($id);
        $model->delete();
    }

    private function forget($id)
    {
        $keys = [
            "salesmen_types",
            "salesmen_types_" . $id,
        ];
        foreach ($keys as $key) {
            cacheForget($key);
        }

    }

    public function getName($request)
    {
        $models = $this->model->select('id','name','name_e');
        if ($request->per_page) {
            return ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            return ['data' => $models->get(), 'paginate' => false];
        }
    }

}
