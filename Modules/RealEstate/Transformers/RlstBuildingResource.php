<?php

namespace Modules\RealEstate\Transformers;

use App\Http\Resources\FileResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Currency;
use App\Models\GeneralMainCostCenters;
use App\Models\Account;
use App\Models\Country;
use App\Models\City;
use App\Models\Avenue;
use Modules\RealEstate\Entities\RlstBuildingType;
use Modules\RealEstate\Entities\RlstBuilding;



class RlstBuildingResource extends JsonResource
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
            "id"                          => $this->id,
            "name"                        => $this->name,
            "name_e"                      => $this->name_e,
            "description"                 => $this->description,
            "description_e"               => $this->description_e,
            "land_area"                   => $this->land_area,
            "building_area"               => $this->building_area,
            "construction_year"           => $this->construction_year,
            "project_id"                  => $this->project_id,
            "country"                     => $this->whenLoaded('country'),
            "governorate"                => $this->whenLoaded('governorate'),
            "city"                        => $this->whenLoaded('city'),
            "avenue"                      => $this->whenLoaded('avenue'),
            "street"                      => $this->whenLoaded('street'),
            "lng"                         => $this->lng,
            "lat"                         => $this->lat,
            "properties"                  => $this->properties,
            "attachments"                 => $this->attachments,
            "module"                      => $this->module,
            "video_link"                  => $this->video_link,
            "media"                       => isset($this->files) ? FileResource::collection($this->files) : null,

            "building_type"               => $this->whenLoaded('buildingType'),

            "company_ownership"           => $this->company_ownership,
            "floors_number"               => $this->floors_number,
            "vaults_number"               => $this->vaults_number,
            "ground_floors_number"        => $this->ground_floors_number,
            "mediums_number"              => $this->mediums_number,
            "elevators_number"            => $this->elevators_number,
            "electricity_meters_number"   => $this->electricity_meters_number,
            "water_meters_number"         => $this->water_meters_number,
            "gas_meters_number"           => $this->gas_meters_number,
            "central_air_conditioning"    => $this->central_air_conditioning,
            "buying_price"                => $this->buying_price,
            "buying_date"                 => $this->buying_date,
            "middleman_cost"              => $this->middleman_cost,
            "registration_cost"           => $this->registration_cost,
            "building_currency"           => $this->whenLoaded('buildingCurrency'),
            //Currency::find($this->building_currency_id),
            "accrued_revenues_account"    => $this->account($this->accrued_revenues_account_id),
            "advance_revenues_account"    => $this->account($this->advance_revenues_account_id),
            "revenues_account"            => $this->account($this->revenues_account_id),
            "discounts_account"           => $this->account($this->discounts_account_id),
            "cash_account"                => $this->account($this->cash_account_id),
            "knet_account_id"             => $this->account($this->knet_account_id),
            "insurance_account_id"        => $this->account($this->insurance_account_id),
            "main_cost_center"            => $this->whenLoaded('mainCostCenter'),
            //GeneralMainCostCenters::find($this->main_cost_center_id),
            "financial_period"            => $this->financial_period,
            'building_wallet'             => $this->buildingWallet,
        ];
    }
}
