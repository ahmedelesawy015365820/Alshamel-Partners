<?php

namespace Modules\PointOfSale\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class PaymentReportResource extends JsonResource
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
            'id' => $this->id,
            'invoice_id' => $this->order->prefix ?? $this->order->invoice_id ?? '',
            'date' => $this->date,
            'method_name'=> $this->paymentMethod->name,
            'method_name_e'=> $this->paymentMethod->name_e,
            'paidBy'=>$this->order->created_by ?? '',
            'amount' => $this->paid,
        ];
    }
}
