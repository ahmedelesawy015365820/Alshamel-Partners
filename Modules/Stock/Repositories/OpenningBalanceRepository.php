<?php

namespace Modules\Stock\Repositories;

use Illuminate\Support\Facades\DB;
use Modules\Stock\Entities\OpenningBalance;


class OpenningBalanceRepository implements OpenningBalanceInterface
{

    private $model;
    private $sspdInterface;
    public function __construct( OpenningBalance $model, StockSalePurchaseDetailInterface $sspdInterface)
    {
        $this->model = $model;
        $this->sspdInterface = $sspdInterface;
    }

    public function all($request)
    {

        $models = $this->model->selectRaw('*, SUM(net) as total_net')->filter($request)->groupBy('wallet_id');

        if ($request->per_page) {
            return ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            return ['data' => $models->get(), 'paginate' => false];
        }
    }

    public function getAll($request)
    {

        $models = $this->model->get();
        return ['data' => $models, 'paginate' => false];
    }

    public function find($id)
    {
        return $this->model->find($id);
    }

    public function getWalletEntier($id)
    {
        $data = $this->model->where('id', $id)->first();
        $model = $this->model->where('date', $data->date)->where('wallet_id', $data->wallet_id)->get();
        if ($model) {
            return ['data' => $model];
        }
    }

    public function create($request)
    {
        DB::transaction(function () use ($request) {
            $this->model->create($request->all());
        });
        $request['type'] = 'Open';
        $this->sspdInterface->create($request);
    }

    public function rowUpdate($request)
    {
        $getdata = $request->get('arrform');

        foreach ($getdata as $value) {

            $model = $this->model->where('id', $value['id'])->first();
            $model->stock_id = $value['stock_id'];
            $model->wallet_id = $value['wallet_id'];
            $model->date = $value['date'];
            $model->qty = $value['qty'];
            $model->price = $value['price'];
            $model->net = $value['net'];
            $model->currency_id = $value['currency_id'];
            $model->update();
        }
        return $model;
    }

    public function delete($id)
    {

        $walletId = $id;
        $models = $this->model->where('wallet_id', $walletId)->get();
        $wallet = StockSalePurchaseDetail::where('wallet_id', $walletId)->first();
        if (empty($wallet)) {
            foreach ($models as $model) {
                $model->delete();
                return $model;
            }
        }
    }


    public function logs($id)
    {
        return $this->model->find($id)->activities()->orderBy('created_at', 'DESC')->get();
    }


    private function forget($id)
    {
        $keys = [
            "openinngBalance",
            "openinngBalance_" . $id,
        ];
        foreach ($keys as $key) {
            cacheForget($key);
        }
    }
    public function checkDate($id)
    {
        $data = $this->model->where("id", $id)->first();
        $stock = StockSalePurchaseDetail::where("date", ">", $data->date)->get();
        if (count($stock) == 0) {
            $model = true;
            return ['data' => $model];
        } else {
            $model = false;
            return ['data' => $model];
        }
    }
}
