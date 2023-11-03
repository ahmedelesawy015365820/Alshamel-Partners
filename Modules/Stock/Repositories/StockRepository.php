<?php

namespace Modules\Stock\Repositories;

use Modules\Stock\Entities\Stock;
use Illuminate\Support\Facades\DB;

class StockRepository implements StockInterface
{

    private $model;
    public function __construct(Stock $model)
    {
        $this->model = $model;
    }

    public function all($request)
    {

        $models = $this->model->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');
        if($request->sspd){
            $models->whereHas('sspd');
        }
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
            $this->model->create($request->all());
            cacheForget("stock");
        });
    }

    public function update($request, $id)
    {
        DB::transaction(function () use ($id, $request) {
            $this->model->where("id", $id)->update($request->all());
            $this->forget($id);
        });
    }

    public function delete($id)
    {
        $model = $this->find($id);
        $this->forget($id);
        $model->delete();
    }


    public function logs($id)
    {
        return $this->model->find($id)->activities()->orderBy('created_at', 'DESC')->get();
    }


    private function forget($id)
    {
        $keys = [
            "stock",
            "stock_" . $id,
        ];
        foreach ($keys as $key) {
            cacheForget($key);
        }
    }
}
