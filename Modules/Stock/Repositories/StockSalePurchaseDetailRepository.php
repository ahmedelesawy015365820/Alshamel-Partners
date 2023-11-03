<?php

namespace Modules\Stock\Repositories;

use Illuminate\Support\Facades\DB;
use Modules\Stock\Entities\StockSalePurchaseDetail;

class StockSalePurchaseDetailRepository implements StockSalePurchaseDetailInterface
{
    private $model;
    public function __construct(StockSalePurchaseDetail $model)
    {
        $this->model =$model;
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
        $checkOld = $this->model->where("wallet_id", $request->wallet_id)->where("stock_id", $request->stock_id)->orderBy('id', 'DESC')->first();



        if (
            $request->has(['qty', 'price', 'fixed_commission'])
            && !is_null($request->input('qty'))
            && !is_null($request->input('price'))
            && !is_null($request->input('fixed_commission'))
        )
        {

            //Extract fixed commission
            $fixed_commission =  (float)$request->input('fixed_commission');

            //Typecast Qty and Price
            $price = (float)$request->input('price');
            $qty = (int)$request->input('qty');

            //If other commission found
            if ($request->has('other_commission') && !is_null($request->input('other_commission'))) {
                $other_commission = (float)$request->input('other_commission');
            } else {
                $other_commission = 0;
            }

            //Calculate Net Value
            $net_value = (float)(($qty * $price) + ($fixed_commission + $other_commission));

            //Calculate Trade Average
            $trade_average = $net_value / $qty;
            $old_sell_qty = 0;
            $old_sell_price = 0;
            //Fetch all latest record from DB
            $last_record = DB::table('stock_sale_purchase_details')->where("wallet_id", $request->wallet_id)->where("stock_id", $request->stock_id)->latest('id')->first();
            if (!is_null($last_record)) { //Last record found
                $last_new_qty = (int)$last_record->new_qty;
                $last_new_average = (float)$last_record->new_average;

                //Calculate new quantity
                // $new_qty = $qty + $last_new_qty;
                $new_qty = $request->type == "Sell" ? $last_new_qty - $qty  : $qty + $last_new_qty;
                //Calculate New Average
                // $new_average = ((($last_new_qty * $last_new_average) + $net_value) / $new_qty);
                //Calculate Profit
                $profit = ($net_value - ($qty * $last_new_average));

                if ($last_record->old_price == 0  &&  $request->type != "Sell") {
                    $old_price =  $last_record->price;
                    $old_sell_qty = $last_record->old_sell_qty;
                    $old_sell_price = $last_record->old_sell_price;
                } elseif ($last_record->old_price != 0 && $last_record->price != 0  && $request->type != "Sell") {
                    $old_price = $last_record->price + $last_record->old_price;
                    $old_sell_qty = $last_record->old_sell_qty;
                    $old_sell_price = $last_record->old_sell_price;
                } elseif ($request->type == "Sell") {

                    $old_price =  $last_record->old_price;
                    $old_sell_qty = $last_record->old_sell_qty != 0 ? $qty + $last_record->old_sell_qty : $qty;
                    $old_sell_price = $last_record->old_sell_price != 0 ? $price + $last_record->old_sell_price : $price;
                }
            } else { //Last record not found
                $last_new_qty = 0;
                $last_new_average = 0;
                $new_qty = $qty;
                // $new_average = 1;
                $profit = 0;
                $old_price = 0;
                if ($request->type == "Sell") {
                    $old_sell_qty = $qty;
                    $old_sell_price = $price;
                }
            } //END: Last record not found
            // dump($old_sell_price);
            // dd($request->All());
            // dd($old_sell_qty);
            if ($request->type == "Buy") {
                $new_average = ((($last_new_qty * $last_new_average) + $net_value) / $new_qty);
            } else if ($request->type == "Sell") {
                if (!is_null($last_record)) {
                    $new_average = $last_record->new_average;
                } else {
                    $new_average = 0;
                }
            } else if($request->type == "Gift"){
                if (!is_null($last_record)) {
                     $new_average = ((($last_new_qty * $last_new_average) + $net_value) / $new_qty);
                } else {
                    $new_average = 0;
                }
            }
            elseif($request->type == "Open"){
                $new_average = ((($last_new_qty * $last_new_average) + $net_value) / $new_qty);
            }
            else{
                 $new_average = 0;
            }
            // dd('old_price', $old_price);
            DB::transaction(function () use (
                $request,
                $net_value,
                $trade_average,
                $last_new_qty,
                $last_new_average,
                $new_qty,
                $new_average,
                $price,
                $profit,
                $old_price,
                $fixed_commission,
                $old_sell_qty,
                $old_sell_price,
                $other_commission,
                $qty
            ) {
                // $this->model->create($request->all());

                // global $net_value, $trade_average, $last_new_qty, $last_new_average,
                //     $new_qty, $new_average, $profit;

                $this->model->create([
                    "wallet_id" => $request->wallet_id,
                    "stock_id" => $request->stock_id,
                    "date" => $request->date,
                    "time" => $request->time,
                    "type" => $request->type,
                    "note" => $request->note,
                    "qty" => $qty,
                    "price" => $price,
                    "fixed_commission" => $fixed_commission,
                    "other_commission" => $other_commission,
                    "net_value" => $net_value,
                    "trade_average" => $trade_average,
                    "old_qty" => $last_new_qty,
                    "old_price" => $old_price,
                    "old_sell_qty" => $old_sell_qty,
                    "old_sell_price" => $old_sell_price,
                    "old_average" => $last_new_average,
                    "new_qty" => $new_qty,
                    "new_average" => $new_average,
                    "profit" => $profit,
                ]);

            });
        } //END: Trade Average
    }

    public function updataData($request)
    {
        $checkOld = $this->model->where("wallet_id", $request->wallet_id)->where("stock_id", $request->stock_id)->orderBy('id', 'DESC')->first();

        if (
            $request->has(['qty', 'price', 'fixed_commission'])
            && !is_null($request->input('qty')) && !is_null($request->input('price'))
            && !is_null($request->input('fixed_commission'))
        ) {

            //Extract fixed commission
            $fixed_commission =  (float)$request->input('fixed_commission');

            //Typecast Qty and Price
            $price = (float)$request->input('price');
            $qty = (int)$request->input('qty');

            //If other commission found
            if ($request->has('other_commission') && !is_null($request->input('other_commission'))) {
                $other_commission = (float)$request->input('other_commission');
            } else {
                $other_commission = 0;
            }

            //Calculate Net Value
            $net_value = (float)(($qty * $price) + ($fixed_commission + $other_commission));

            //Calculate Trade Average
            $trade_average = $net_value / $qty;
            $old_sell_qty = 0;
            $old_sell_price = 0;
            //Fetch all latest record from DB
            $last_record = DB::table('stock_sale_purchase_details')->where("wallet_id", $request->wallet_id)->where("stock_id", $request->stock_id)->where('id', '!=', $request->id)->latest('id')->first();
            if (!is_null($last_record)) { //Last record found
                $last_new_qty = (int)$last_record->new_qty;
                $last_new_average = (float)$last_record->new_average;

                //Calculate new quantity
                // $new_qty = $qty + $last_new_qty;
                $new_qty = $request->type == "Sell" ? $last_new_qty - $qty  : $qty + $last_new_qty;
                //Calculate New Average
                // $new_average = ((($last_new_qty * $last_new_average) + $net_value) / $new_qty);
                //Calculate Profit
                $profit = ($net_value - ($qty * $last_new_average));

                if ($last_record->old_price == 0  &&  $request->type != "Sell") {
                    $old_price =  $last_record->price;
                    $old_sell_qty = $last_record->old_sell_qty;
                    $old_sell_price = $last_record->old_sell_price;
                } elseif ($last_record->old_price != 0 && $last_record->price != 0  && $request->type != "Sell") {
                    $old_price = $last_record->price + $last_record->old_price;
                    $old_sell_qty = $last_record->old_sell_qty;
                    $old_sell_price = $last_record->old_sell_price;
                } elseif ($request->type == "Sell") {
                    $old_price =  $last_record->old_price;
                    $old_sell_qty = $last_record->old_sell_qty != 0 ? $qty + $last_record->old_sell_qty : $qty;
                    $old_sell_price = $last_record->old_sell_price != 0 ? $price + $last_record->old_sell_price : $price;
                }
            } else { //Last record not found
                $last_new_qty = 0;
                $last_new_average = 0;
                $new_qty = $qty;
                // $new_average = 1;
                $profit = 0;
                $old_price = 0;
                if ($request->type == "Sell") {
                    $old_sell_qty = $qty;
                    $old_sell_price = $price;
                }
            } //END: Last record not found
            // dump($old_sell_price);
            // dd($request->All());
            // dd($old_sell_qty);
            if ($request->type == "Buy") {
                $new_average = ((($last_new_qty * $last_new_average) + $net_value) / $new_qty);
            } else if ($request->type == "Sell") {
                if (!is_null($last_record)) {
                    $new_average = $last_record->new_average;
                } else {
                    $new_average = 0;
                }
            }
            DB::transaction(function () use (
                $request,
                $net_value,
                $trade_average,
                $last_new_qty,
                $last_new_average,
                $new_qty,
                $new_average,
                $price,
                $profit,
                $old_price,
                $fixed_commission,
                $old_sell_qty,
                $old_sell_price,
                $other_commission,
                $qty
            ) {
                // $this->model->create($request->all());

                // global $net_value, $trade_average, $last_new_qty, $last_new_average,
                //     $new_qty, $new_average, $profit;

                $this->model->where('id', $request->id)->update([
                    "wallet_id" => $request->wallet_id,
                    "stock_id" => $request->stock_id,
                    "date" => $request->date,
                    "time" => $request->time,
                    "type" => $request->type,
                    "note" => $request->note,
                    "qty" => $qty,
                    "price" => $price,
                    "fixed_commission" => $fixed_commission,
                    "other_commission" => $other_commission,
                    "net_value" => $net_value,
                    "trade_average" => $trade_average,
                    "old_qty" => $last_new_qty,
                    "old_price" => $old_price,
                    "old_sell_qty" => $old_sell_qty,
                    "old_sell_price" => $old_sell_price,
                    "old_average" => $last_new_average,
                    "new_qty" => $new_qty,
                    "new_average" => $new_average,
                    "profit" => $profit,
                ]);
                $this->forget($request->id);
            });
        } //END: Trade Average
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
            "stockSalePurchaseDetail",
            "stockSalePurchaseDetail_" . $id,
        ];
        foreach ($keys as $key) {
            cacheForget($key);
        }
    }
}
