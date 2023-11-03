<?php

namespace App\Repositories\Bank;

use Illuminate\Support\Facades\DB;

class BankRepository implements BankInterface
{

    public function __construct(private \App\Models\Bank $model)
    {}

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
        return $this->model->data()->find($id);
    }

    public function create($request)
    {
        return DB::transaction(function () use ($request) {
            return $this->model->create($request->all());
            cacheForget("banks");
        });
    }

    public function update($request, $id)
    {
        DB::transaction(function () use ($id, $request) {
            $this->model->where("id", $id)->update($request->all());
            // $this->forget($id);
        });

    }

    public function delete($id)
    {
        $model = $this->find($id);
        // $this->forget($id);
        $model->delete();
    }

    public function logs($id)
    {
        return $this->model->find($id)->activities()->orderBy('created_at', 'DESC')->get();
    }

    private function forget($id)
    {
        $keys = [
            "banks",
            "banks_" . $id,
        ];
        foreach ($keys as $key) {
            cacheForget($key);
        }

    }

    public function getName($request)
    {
        $models = $this->model->select('id', 'name', 'name_e')->when($request->country_id, function ($query) use ($request) {
            $query->where('country_id', $request->country_id);
        });

        if ($request->per_page) {
            return ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            return ['data' => $models->get(), 'paginate' => false];
        }
    }

}
