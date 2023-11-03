<?php

namespace Modules\PointOfSale\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class InventoryResource extends JsonResource
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
            'sku' => $this->sku,
            'bar_code' => $this->bar_code,
            'title' => $this->product ? $this->product->title : null,
            'title_e' => $this->product ? $this->product->title_e : null,
            'variant_title' => $this->variant_title,
            'category_name' => $this->product->category ? $this->product->category->name : null,
            'category_name_e' => $this->product->category ? $this->product->category->name_e : null,
            'group_name' => $this->product->group ? $this->product->group->name : null,
            'group_name_e' => $this->product->group ? $this->product->group->name_e : null,
            'brand_name' => $this->product->brand ? $this->product->brand->name : null,
            'brand_name_e' => $this->product->brand ? $this->product->brand->name_e : null,
            'purchase_price' => $this->purchase_price,
            'selling_price' => $this->selling_price,
            'quantity'=> $this->product->orderItems->sum('quantity'),

        ];
    }
}
