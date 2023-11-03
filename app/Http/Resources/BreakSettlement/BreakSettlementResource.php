<?php

namespace App\Http\Resources\BreakSettlement;

use Illuminate\Http\Resources\Json\JsonResource;

class BreakSettlementResource extends JsonResource
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
            'document_id'     => $this->document_id,
            'document_number' => $this->document_number,
            'amount'          => $this->amount,
            'break_id'        => $this->break_id,
            'break_break_id'  => $this->break_break_id,
        ];
    }
}
