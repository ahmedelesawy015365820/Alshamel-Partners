<?php

namespace Modules\PointOfSale\Transformers;

use App\Models\Attribute;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductVariantResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $orderItemQuantity = $this->orderItemQuantity;

        return [
            'id' => $this->id,
            'sku' => $this->sku,
            'variant_title' => $this->variant_title,
            'attribute_values' => $this->attribute_values,
            'variant_details' => $this->variant_details,
            'purchase_price' => $this->purchase_price,
            'selling_price' => $this->selling_price,
            'enabled' => $this->enabled,
            'isNotify' => $this->isNotify,
            'bar_code' => $this->bar_code,
            're_order' => $this->re_order,
            'product_id' => $this->product_id,
            'product' => $this->product,
            'quantity' => $this->remaining_quantity,
            'product_quantity' => $orderItemQuantity?$orderItemQuantity->quantity:0,
            'product_attributes' => Attribute::join('pos_product_attribute_values', function ($join) {
                $join->on('general_attributes.id', '=', 'pos_product_attribute_values.attribute_id')
                    ->where('pos_product_attribute_values.variant_id', '=', $this->id)
                    ->whereNull('pos_product_attribute_values.deleted_at');
            })->get(),
            "media" => $this->media ? $this->transformMedia($this->media) : null,
        ];
    }

    private function transformMedia($files)
    {
        $media = collect();

        foreach ($files as $file) {
            $media->push([
                'name' => $file->name,
            ]);
        }

        return $media->isNotEmpty() ? $media : null;
    }
}
