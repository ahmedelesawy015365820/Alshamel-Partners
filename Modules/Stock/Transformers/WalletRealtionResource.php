<?php

namespace Modules\Stock\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class WalletRealtionResource extends JsonResource
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
            'name' => $this->name,
            'name_e' => $this->name_e,
            'stock_balance' => $this->stock_balance,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
