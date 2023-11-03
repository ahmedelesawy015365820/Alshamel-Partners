<?php

namespace App\Repositories\Store;

use App\Models\Store;
use Illuminate\Support\Facades\DB;

class StoreRepository implements StoreInterface
{

    public function __construct(private Store $model)
    {
        $this->model = $model;

    }

    public function all($request)
    {
        $models = $this->model->Data()->filter($request)->with('branch')->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

        if ($request->per_page) {
            return ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            return ['data' => $models->get(), 'paginate' => false];
        }
    }

    public function find($id)
    {
        return $this->model->Data()->find($id);
    }

    public function create($request)
    {
        DB::transaction(function () use ($request) {
            $this->model->create($request->all());
            cacheForget("stores");
        });
    }

    public function update($request, $id)
    {
        DB::transaction(function () use ($id, $request) {

            $this->model->where("id", $id)->update($request->all());

            $this->forget($id);

        });

    }

    public function logs($id)
    {
        return $this->model->find($id)->activities()->orderBy('created_at', 'DESC')->get();
    }
    public function delete($id)
    {
        $model = $this->find($id);
        $this->forget($id);
        $model->delete();
    }
    private function forget($id)
    {
        $keys = [
            "stores",
            "stores_" . $id,
        ];
        foreach ($keys as $key) {
            cacheForget($key);
        }

    }

    public function getName($request)
    {
        $models = $this->model->select('id', 'name', 'name_e')->where('is_active', 'active');

        if ($request->per_page) {
            return ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            return ['data' => $models->get(), 'paginate' => false];
        }
    }

}
