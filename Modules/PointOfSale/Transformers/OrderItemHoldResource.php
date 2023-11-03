<?php

namespace Modules\PointOfSale\Transformers;

use App\Http\Resources\Tax\TaxResource;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemHoldResource extends JsonResource
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
            'id'  => $this->id,
            'discount'  => $this->discount,
            'note'  => $this->note,
            'order_id'  => $this->order_id,
            'price' => $this->price,
            'product_id'  => $this->product_id,
            'product'  => new ProductSaleResource($this->product),
            'quantity' => $this->quantity,
            'sub_total'  =>$this->sub_total,
            'tax_id'  => $this->tax_id,
            'variant_id' => $this->variant_id,
        ];
    }
}
