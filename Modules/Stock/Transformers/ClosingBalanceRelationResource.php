<?php

namespace Modules\Stock\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Stock\StoctRelationResource;

class ClosingBalanceRelationResource extends JsonResource
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
            'stock' => new  StoctRelationResource($this->stock),
            'stock_id' => $this->stock_id,
            'date' => $this->date,
            'amount' => $this->amount,
        ];
    }
}
