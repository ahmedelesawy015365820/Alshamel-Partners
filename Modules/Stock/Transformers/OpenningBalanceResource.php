<?php

namespace Modules\Stock\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Currency\CurrencyRelationResource;
use App\Http\Resources\Stock\StoctRelationResource;
use App\Http\Resources\Wallet\WalletRelationResource;

class OpenningBalanceResource extends JsonResource
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
            'wallet' => new WalletRelationResource($this->wallet),
            'stock_id' => $this->stock_id,
            'stock' => new StoctRelationResource($this->stock),
            'date' => $this->date,
            'total_net' => $this->total_net,
            'qty' => $this->qty,
            'price' => $this->price,
            'net' => $this->net,
            'currency_id' => $this->currency_id,
            'currency' => new CurrencyRelationResource($this->currency),

        ];
    }
}
