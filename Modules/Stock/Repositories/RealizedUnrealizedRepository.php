<?php

namespace Modules\Stock\Repositories;

use Illuminate\Support\Facades\DB;
use Modules\Stock\Entities\StockSalePurchaseDetail;

class RealizedUnrealizedRepository implements RealizedUnrealizedInterface
{
    private $model;
    public function __construct(StockSalePurchaseDetail $model)
    {
        $this->model = $model;
    }

    // public function all($request)
    // {
    //     // if($request->has('wallet_id')  && !empty($request->wallet_id) &&  $request->has('start_date')  && !empty($request->start_date)  &&  $request->has('end_date')  && !empty($request->end_date)){
    //     //     $models = $this->model->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC')
    //     //     ->whereBetween('date',[$request->start_date, $request->end_date])->where('wallet_id',$request->wallet_id);
    //     //     //  dd($models->get());
    //     //     if ($request->per_page) {
    //     //         return ['data' => $models->paginate($request->per_page), 'paginate' => true];
    //     //     } else {
    //     //         return ['data' => $models->get(), 'paginate' => false];
    //     //     }
    //     // }
    //     // dd($request);
    //     $stock=[];
    //      if($request->has('stock_id')  && !empty($request->stock_id) ){
    //         array_push($stock,$request->stock_id);
    //      }else{
    //         $stock=Stock::limit($request->per_page)->offset(($request->page - 1) * $request->per_page)->orderBy('id', 'DESC')->get();
    //      }
    //     $sellAmount=0;
    //     $sellqty=0;
    //     $buyAmount=0;
    //     $buyqty=0;
    //     $buyprice=0;
    //     $obj;
    //     $ary=[];
    //     $stockId = $this->model->where('wallet_id',$request->wallet_id)->groupBy('stock_id')->pluck('stock_id');
    //     // dd($stockId);
    //     $models = $this->model->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');
    //     // $models = $this->model->select("*", DB::raw('SUM(price) as price'),DB::raw('SUM(qty) as qty'))->whereBetween('date',[$request->start_date, $request->end_date]);
    //     foreach($stockId as $stocks){
    //         $models = $this->model->whereBetween('date',[$request->start_date, $request->end_date])->where('wallet_id',$request->wallet_id);
    //         $models->where('stock_id',$stocks);
    //         $models = $models->get();
    //         foreach($models as $value){
    //             if($value->type == "Sell"){
    //                 $sellAmount+=$value->price;
    //                 $sellqty+=$value->qty;
    //             }elseif($value->type == "Buy"){
    //                 $buyAmount+=$value->price;
    //                 $buyqty+=$value->qty;
    //             }
    //             $obj=$value;
    //         }
    //         $obj->sellAmount=$sellAmount?$sellAmount:0;
    //         $obj->sellqty=$sellqty?$sellqty:0;
    //         $obj->buyAmount=$buyAmount?$buyAmount:0;
    //         $obj->buyqty=$buyqty?$buyqty:0;
    //         array_push($ary,$obj);
    //    }
    //     // if ($request->per_page) {
    //     //     return ['data' => $models->paginate($request->per_page), 'paginate' => true];
    //     // } else {
    //         return ['data' => $ary,'paginate' => false];
    //     // }
    // }


    public function all($request)
    {
        $models = $this->model->filter($request);
        $models->whereBetween('date', [$request->start_date, $request->end_date])->where('wallet_id', $request->wallet_id);
        $models->whereRaw('id IN (select MAX(id) FROM sys_stock_sale_purchase_details GROUP BY stock_id)');

        if ($request->per_page) {
            $data = $models->paginate($request->per_page);
            $paginate = true;
        } else {
            $data = $models->get();
            $paginate = false;
        }
        // dd($data);
        return ['data' => $data, 'paginate' => $paginate];
    }



    // public function find($id)
    // {
    //     return $this->model->find($id);
    // }

    // public function create($request)
    // {
    //     if (
    //         $request->has(['qty', 'price', 'fixed_commission'])
    //         && !is_null($request->input('qty')) && !is_null($request->input('price'))
    //         && !is_null($request->input('fixed_commission'))
    //     ) {

    //         //Extract fixed commission
    //         $fixed_commission =  (float)$request->input('fixed_commission');

    //         //Typecast Qty and Price
    //         $price = (float)$request->input('price');
    //         $qty = (int)$request->input('qty');

    //         //If other commission found
    //         if ($request->has('other_commission') && !is_null($request->input('other_commission'))) {
    //             $other_commission = (float)$request->input('other_commission');
    //         } else {
    //             $other_commission = 0;
    //         }

    //         //Calculate Net Value
    //         $net_value = (float)(($qty * $price) + ($fixed_commission + $other_commission));

    //         //Calculate Trade Average
    //         $trade_average = $net_value / $qty;

    //         //Fetch all latest record from DB
    //         $last_record = DB::table('stock_sale_purchase_details')->latest('id')->first();

    //         if (!is_null($last_record)) { //Last record found
    //             $last_new_qty = (int)$last_record->new_qty;
    //             $last_new_average = (float)$last_record->new_average;

    //             //Calculate new quantity
    //             $new_qty = $qty + $last_new_qty;
    //             //Calculate New Average
    //             $new_average = ((($last_new_qty * $last_new_average) + $net_value) / $new_qty);
    //             //Calculate Profit
    //             $profit = ($net_value - ($qty * $last_new_average));
    //         } else { //Last record not found
    //             $last_new_qty = 0;
    //             $last_new_average = 0;
    //             $new_qty = 0;
    //             $new_average = 1;
    //             $profit = 0;
    //         } //END: Last record not found

    //         DB::transaction(function () use (
    //             $request,
    //             $net_value,
    //             $trade_average,
    //             $last_new_qty,
    //             $last_new_average,
    //             $new_qty,
    //             $new_average,
    //             $price,
    //             $profit,

    //             $fixed_commission,
    //             $other_commission,
    //             $qty
    //         ) {
    //             // $this->model->create($request->all());

    //             // global $net_value, $trade_average, $last_new_qty, $last_new_average,
    //             //     $new_qty, $new_average, $profit;

    //             $this->model->create([
    //                 "wallet_id" => $request->wallet_id,
    //                 "stock_id" => $request->stock_id,
    //                 "date" => $request->date,
    //                 "time" => $request->time,
    //                 "type" => $request->type,
    //                 "note" => $request->note,
    //                 "qty" => $qty,
    //                 "price" => $price,
    //                 "fixed_commission" => $fixed_commission,
    //                 "other_commission" => $other_commission,
    //                 "net_value" => $net_value,
    //                 "trade_average" => $trade_average,
    //                 "old_qty" => $last_new_qty,
    //                 "old_average" => $last_new_average,
    //                 "new_qty" => $new_qty,
    //                 "new_average" => $new_average,
    //                 "profit" => $profit,
    //             ]);
    //             cacheForget("stockSalePurchaseDetail");
    //         });
    //     } //END: Trade Average
    // }

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


    // private function forget($id)
    // {
    //     $keys = [
    //         "stockSalePurchaseDetail",
    //         "stockSalePurchaseDetail_" . $id,
    //     ];
    //     foreach ($keys as $key) {
    //         cacheForget($key);
    //     }
    // }
}
