<?php

namespace Modules\BoardsRent\Transformers\Panel;

use App\Http\Resources\Avenue\RelationAvenueResource;
use App\Http\Resources\Category\RelationCategoryResource;
use App\Http\Resources\City\RelationCityResource;
use App\Http\Resources\Governorate\RelationGovernorateResource;
use App\Http\Resources\Street\RelationStreetResource;
use Illuminate\Http\Resources\Json\JsonResource;

class RelationBRentPanelResource extends JsonResource
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
            'id'             => $this->id,
            'name'           => $this->name,
            'name_e'         => $this->name_e,
            "code"           => $this->code,
            'face'           => $this->face,
            'category'       => new RelationCategoryResource($this->category),
            "governorate"    => new RelationGovernorateResource($this->governorate),
            'city'           => new RelationCityResource($this->city),
            'avenue'         => new RelationAvenueResource($this->avenue),
            'street'         => new RelationStreetResource($this->street),
        ];
    }
}
