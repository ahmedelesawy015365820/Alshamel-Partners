<?php

namespace Modules\PointOfSale\Transformers;

use App\Http\Resources\FileResource;
use App\Models\Attribute;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'description' => $this->description,
            'description_e' => $this->description_e,
            'taxable' => $this->taxable,
            'tax_type' => $this->tax_type,
            'product_type' => $this->product_type,
            'is_quantity' => $this->is_quantity,
            'created_by' => $this->created_by,
            'branch_id' => $this->branch_id,
            'category_id' => $this->category_id,
            'brand_id' => $this->brand_id,
            'unit_id' => $this->unit_id,
            'group_id' => $this->group_id,
            'tax_id' => is_numeric($this->tax_id) ? intval($this->tax_id) : $this->tax_id,
            'tax' => is_numeric($this->tax_id) ? $this->tax : $this->tax_id,
            'branch' => $this->branch,
            'category' => $this->category,
            'brand' => $this->brand,
            'unit' => $this->unit,
            'group' => $this->group,
            'product_variant' => ProductVariantResource::collection($this->product_variant),
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
            "media" => isset($this->files) ? FileResource::collection($this->files) : null,
            "inventory" => $this->orderItems->sum('quantity'),
        ];
    }
}
