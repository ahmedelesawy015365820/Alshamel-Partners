<?php

namespace Modules\PointOfSale\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
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
                "id"            => $this->id,
                "product_id"    => $this->product_id,
                'product'  => new ProductSaleResource($this->product),
                "variant_id"    => $this->variant_id,
                "type"          => $this->type,
                "quantity"      => $this->quantity,
                "price"         => $this->price,
                "discount"      => $this->discount,
                "sub_total"     => $this->sub_total,
                "tax_id"        => $this->tax_id,
                "note"        => $this->note,
                "order_id"      => $this->order_id,
                "parent_id"     => $this->parent_id,
                "item_discount" => $this->item_discount,
                "item_count"    => $this->item_count,
                "tax"          =>  collect($this->tax)->only(['id','name','name_e','percentage']),
        ];
    }
}
