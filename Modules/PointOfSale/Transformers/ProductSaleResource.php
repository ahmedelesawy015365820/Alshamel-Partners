<?php

namespace Modules\PointOfSale\Transformers;

use App\Http\Resources\FileResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductSaleResource extends JsonResource
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
            'title' => $this->title,
            'title_e' => $this->title_e,
            'product_type' => $this->product_type,
            'is_quantity' => $this->is_quantity,
            'created_by' => $this->created_by,
            'branch_id' => $this->branch_id,
            'tax' => is_numeric($this->tax_id) ? $this->tax : null,
            'product_variant' => ProductVariantSaleResource::collection($this->product_variant),
            "media" => isset($this->files) ? FileResource::collection($this->files) : null,
        ];
    }
}
