<?php

namespace Modules\Stock\Repositories;

use Illuminate\Support\Facades\DB;
use Modules\Stock\Entities\ClosingBalance;

class ClosingBalanceRepository implements ClosingBalanceInterface
{

    private $model;
    public function __construct(ClosingBalance $model)
    {
        $this->model = $model;
    }

    public function all($request)
    {

        $models = $this->model->selectRaw('*, SUM(amount) as total_amount')->filter($request)->groupBy('date');

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
            cacheForget("closingBalance");
        });
    }

    public function rowUpdate($request)
    {
        $getdata = $request->get('arrform');
        foreach ($getdata as $value) {
            $model = $this->model->where('id', $value['id'])->first();
            $model->stock_id = $value['stock_id'];
            $model->amount = $value['amount'];
            $model->date = $value['date'];
            $model->update();
            return $model;
        }
    }

    public function delete($date)
    {
        $models = $this->model->where('date', $date)->get();
        // $this->forget($date);
        foreach ($models as $model) {
            $model->delete();
            return $model;
        }
    }

    public function getAllEntier($date)
    {
        $model = $this->model->where('date', $date)->get();
        if ($model) {
            return ['data' => $model];
        }
    }


    public function logs($id)
    {
        return $this->model->find($id)->activities()->orderBy('created_at', 'DESC')->get();
    }


    private function forget($id)
    {
        $keys = [
            "closingBalance",
            "closingBalance_" . $id,
        ];
        foreach ($keys as $key) {
            cacheForget($key);
        }
    }
}
