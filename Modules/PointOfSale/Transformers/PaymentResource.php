<?php

namespace Modules\PointOfSale\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
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
            'date' => $this->date,
            'paid' => $this->paid,
            'exchange' => $this->exchange,
            'payment_method_id' => $this->paymentMethod,
            'options' => $this->options,
            'order_id' => $this->order_id,
            'cash_register_id' => $this->cash_register_id,
            'is_active' => $this->is_active,
        ];
    }
}
