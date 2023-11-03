<?php

namespace Modules\Stock\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Wallet\WalletRelationResource;
use App\Http\Resources\Stock\StoctRelationResource;

class ProfitReportsResource extends JsonResource
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
            'id' => $this->id,
            'wallet_id' => $this->wallet_id,
            'stock_id' => $this->stock_id,
            'stock' => new StoctRelationResource($this->stock),
            'wallet' => new WalletRelationResource($this->wallet),
            'date' => $this->date,
            'time' => $this->time,
            'type' => $this->type,
            'note' => $this->note,
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
        ];
    }
}
