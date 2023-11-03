<?php

namespace App\Http\Resources\Supplier;

use App\Http\Resources\BankAccount\BankAccountResource;
use App\Http\Resources\BankAccount\RelationBankAccountResource;
use App\Http\Resources\City\CityResource;
use App\Http\Resources\City\RelationCityResource;
use App\Http\Resources\Country\CountryResource;
use App\Http\Resources\Country\RelationCountryResource;
use App\Http\Resources\CustomerGroup\CustomerGroupResource;
use App\Http\Resources\CustomerGroup\RelationCustomerGroupResource;
use App\Http\Resources\Employee\EmployeeResource;
use App\Http\Resources\Employee\RelationEmployeeResource;
use App\Http\Resources\FileResource;
use App\Http\Resources\Salesman\RelationSalesmanResource;
use App\Http\Resources\Salesman\SalesmanResource;
use Illuminate\Http\Resources\Json\JsonResource;

class SupplierResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [

            'id'=>$this->id,
            'name'=>$this->name,
            'name_e'=>$this->name_e,
            'phone'=>$this->phone,
            'email'=>$this->email,
            'country_id'=>$this->country_id,
            'city_id'=>$this->city_id,
            'rp_code'=>$this->rp_code,
            'nationality'=>$this->nationality,
            'bank_account_id'=>$this->bank_account_id,
            'contact_person'=>$this->contact_person,
            'contact_phone'=>$this->contact_phone,
            'national_id'=>$this->national_id,
            'whatsapp'=>$this->whatsapp,
            'passport_no'=>$this->passport_no,
            'note'=>$this->note,
            'code_country'=>$this->code_country,
            'facebook'=>$this->facebook,
            'instagram'=>$this->instagram,
            'linkedin'=>$this->linkedin,
            'snapchat'=>$this->snapchat,
            'twitter'=>$this->twitter,
            'website'=>$this->website,
            'salesman_id'=>$this->salesman_id,
            'employee_id'=>$this->employee_id,
            'customer_source_id'=>$this->customer_source_id,
            'customer_group_id'=>$this->customer_group_id,
            'sector_id'=>$this->sector_id,
            'customer_id'=>$this->customer_id,
            "salesmen" => new RelationSalesmanResource($this->salesmen),
            "employee" => new RelationEmployeeResource($this->employee),
            "country" => new RelationCountryResource($this->country),
            "city" => new RelationCityResource($this->city),
            "bankAccount" => new RelationBankAccountResource($this->bankAccount),
            "customerGroup" => new RelationCustomerGroupResource($this->customerGroup),
            "media" => isset($this->files) ? FileResource::collection($this->files) : null,
//            'customer_main_category'=>$this->customer_main_category,
//            'customer_sub_category'=>$this->customer_sub_category,


        ];
    }
}
