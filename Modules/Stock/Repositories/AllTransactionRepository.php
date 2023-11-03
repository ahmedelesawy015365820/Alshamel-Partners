<?php

namespace Modules\Stock\Repositories;

use Illuminate\Support\Facades\DB;
use Modules\Stock\Entities\StockSalePurchaseDetail;

class AllTransactionRepository implements AllTransactionInterface
{

    private $model;
    public function __construct(StockSalePurchaseDetail $model)
    {
        $this->model = $model;
    }
    public function all($request)
    {

        $first = $this->model->whereDate('date', '>=', $request->start_date)
            ->where('wallet_id', $request->wallet_id)
            ->where('stock_id', $request->stock_id)
            ->first();

        $models = $this->model
            ->whereBetween('date', [$request->start_date, $request->end_date])
            ->where('wallet_id', $request->wallet_id)
            ->where('stock_id', $request->stock_id)
            // ->whereRaw('id in (select max(id) from sys_stock_sale_purchase_details group by (stock_id))')
            ->orderBy('id', 'desc')->get();


        // if ($request->has('wallet_id')) {
        //     $models = $this->model->where('wallet_id', $request->wallet_id);
        //     // $models =  $this->model->whereRaw('id IN (select MAX(id) FROM sys_stock_sale_purchase_details GROUP BY stock_id)');
        // }
        // if ($request->has('stock_id')) {
        //     $models = $models->
        // }

        // if (!empty($request->stock_id)) {
        //     $models->whereRaw('id IN (select MAX(id) FROM sys_stock_sale_purchase_details GROUP BY stock_id)');
        // } else {
        //     $models =  $this->model->whereRaw('id IN (select MAX(id) FROM sys_stock_sale_purchase_details GROUP BY stock_id)');
        // }


         $models['first'] = $first;

        return ['data' => $models, 'paginate' => false];
    }

    // public function find($id)
    // {
    //     return $this->model->find($id);
    // }

    public function create($request)
    {
        DB::transaction(function () use ($request) {
            $this->model->create($request->all());
            cacheForget("alltransaction");
        });
    }



    // public function update($request, $id)
    // {
    //     DB::transaction(function () use ($id, $request) {
    //         $this->model->where("id", $id)->update($request->all());
    //         $this->forget($id);
    //     });
    // }

    // public function delete($id)
    // {
    //     $model = $this->find($id);
    //     $this->forget($id);
    //     $model->delete();
    // }


    // public function logs($id)
    // {
    //     return $this->model->find($id)->activities()->orderBy('created_at', 'DESC')->get();
    // }


    private function forget($id)
    {
        $keys = [
            "currency",
            "currency_" . $id,
        ];
        foreach ($keys as $key) {
            cacheForget($key);
        }
    }
}
