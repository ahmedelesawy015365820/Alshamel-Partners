<?php

namespace Modules\Stock\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Wallet\WalletRelationResource;
use App\Http\Resources\Stock\StoctRelationResource;
use App\Http\Resources\ClosingBalance\ClosingBalanceRelationResource;
use App\Models\StockSalePurchaseDetail;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;

class AllTransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' =>  $this->id,
            'wallet_id' => $this->wallet_id,
            'stock_id' => $this->stock_id,
            'stock' => new StoctRelationResource($this->stock),
            'wallet' => new WalletRelationResource($this->wallet),
            'closing' => new ClosingBalanceRelationResource($this->closing),
            'date' => $this->date,
            'time' => $this->time,
            'type' => $this->type,
            'sellAmount' => $this->sellAmount,
            'first' => $this->first,
            "buyAmount" => $this->buyAmount,
            "buyqty" => $this->buyqty,
            'sellqty' => $this->sellqty,
            "old_price" => $this->old_price,
            "old_sell_qty" => $this->old_sell_qty,
            "old_sell_price" => $this->old_sell_price,
            'qty' => $this->qty,
            'price' => $this->price,
            'fixed_commission' => $this->fixed_commission,
            'other_commission' => $this->other_commission,
            'net_value' => $this->net_value,
            'trade_average' => $this->trade_average,
            'old_qty' => $this->old_qty,
            'old_average' => $this->old_average,
            'new_qty' => $this->new_qty,
            'new_average' => $this->new_average,
            'profit' => $this->profit,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'raw_arr' => $this->raw_arr,
            'last' => $this->sumValue($this->date, $this->wallet_id, $this->stock_id, $request->end_date),
        ];
    }

    public function sumValue($date, $wallet_id, $stock_id, $endDate)
    {
        $data = StockSalePurchaseDetail::whereDate('date', '>=', $date)
            ->whereDate('date', '<=', $endDate)
            ->where('wallet_id', $wallet_id)
            ->where('stock_id', $stock_id)
            ->where('type', 'Buy')
            ->select("*", DB::raw('SUM(qty*price) as t_price'), DB::raw('SUM(qty) as t_qty'))
            ->get();
        return $data;
    }
}
