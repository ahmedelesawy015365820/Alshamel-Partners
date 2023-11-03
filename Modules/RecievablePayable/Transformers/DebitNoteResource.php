<?php

namespace Modules\RecievablePayable\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class DebitNoteResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            "id"=>$this->id,
            "date"=>$this->date,
            "currency_id"=>$this->currency_id,
            "customer_id"=>$this->customer_id,
            "customer" => $this->customer,
            "currency" => $this->currency,
            "debit"=>$this->debit,
            "credit"=>$this->credit,
            "local_debit"=>$this->local_debit,
            "local_credit"=>$this->local_credit,
            "count"=>$this->count,
            "rate"=>$this->rate,
            "type_document"=>$this->type_document,
        ];
    }
}
