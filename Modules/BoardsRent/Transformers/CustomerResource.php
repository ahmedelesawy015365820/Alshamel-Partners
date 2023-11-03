<?php

namespace Modules\BoardsRent\Transformers;

use App\Http\Resources\Avenue\AvenueResource;
use App\Http\Resources\City\CityResource;
use App\Http\Resources\Country\CountryResource;
use App\Http\Resources\Salesman\SalesmanResource;
use App\Http\Resources\Sector\SectorResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\BoardsRent\Entities\CustomerSource;
use Modules\BoardsRent\Transformers\CustomerSourceResource;

class CustomerResource extends JsonResource
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
            'name' => $this->name,
            'name_e' => $this->name_e,
            'phone' => $this->phone,
            'email' => $this->email,
            "country" => new CountryResource($this->country),
            "city" => new CityResource($this->city),
            "avenue" => new AvenueResource($this->avenue),
            'contact_person' => $this->contact_person,
            'contact_phone' => $this->contact_phone,
            'whatsapp' => $this->whatsapp,
            'facebook' => $this->facebook,
            'instagram' => $this->instagram,
            'linkedin' => $this->linkedin,
            'twitter' => $this->twitter,
            'snapchat' => $this->snapchat,
            'website' => $this->website,
            'customer_main_category_id' => $this->customer_main_category_id,
            "customer_sub_category_id" => $this->customer_sub_category_id,
            'salesman' => new SalesmanResource($this->salesman),
            "customer_source" => new CustomerSourceResource($this->customerSource),
            "sector" => new SectorResource($this->sector),
            "sector_id" => $this->sector_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
