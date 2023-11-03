<?php

namespace Modules\BoardsRent\Transformers\Panel;

use App\Http\Resources\Avenue\RelationAvenueResource;
use App\Http\Resources\Category\RelationCategoryResource;
use App\Http\Resources\City\RelationCityResource;
use App\Http\Resources\Country\RelationCountryResource;
use App\Http\Resources\Governorate\RelationGovernorateResource;
use App\Http\Resources\Street\RelationStreetResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\BoardsRent\Transformers\UnitStatus\RelationBRentUnitStatusResource;

class AllBRentPanelResource extends JsonResource
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
            "price"          => $this->price,
            "code"           => $this->code,
            "new_code"       => $this->new_code,
            "size"           => $this->size,
            "note"           => $this->note,
            'face'           => $this->face,
            'unit_status_id' => $this->unit_status_id,
            "category_id"    => $this->category_id,
            'governorate_id' => $this->governorate_id,
            "city_id"        => $this->city_id,
            "country_id"     => $this->country_id,
            'avenue_id'      => $this->avenue_id,
            "street_id"      => $this->street_id,
            'lat'            => $this->lat,
            'lng'            => $this->lng,
            'description'    => $this->description,
            'description_e'  => $this->description_e,
            'unit_status'    => new RelationBRentUnitStatusResource($this->unitStatus),
            'category'       => new RelationCategoryResource($this->category),
            "country"        => new RelationCountryResource($this->country),
            "governorate"    => new RelationGovernorateResource($this->governorate),
            'city'           => new RelationCityResource($this->city),
            'avenue'         => new RelationAvenueResource($this->avenue),
            'street'         => new RelationStreetResource($this->street),

//            'itemBreakDowns' => $this->itemBreakDowns,

        ];
    }
}
