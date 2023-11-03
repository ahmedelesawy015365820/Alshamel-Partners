<?php

namespace Modules\RealEstate\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\RealEstate\Entities\RlstCategoryItem;
use Modules\RealEstate\Entities\RlstItem;

class RlstCategoriesItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $category = RlstCategoryItem::where('id', $this->category_item_id)->first();
        $item = RlstItem::where('id',$this->item_id)->first();

        return [
            'id' => $this->id,
            'category_item_id' => $this->category_item_id,
            'item_id' => $this->item_id,

            'category' => new RlstCategoryItemResource($category),
            'item' => new RlstItemResource($item),
        ];
    }
}
