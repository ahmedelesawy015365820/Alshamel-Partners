<?php

namespace App\Http\Resources\Company;

use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResource extends JsonResource
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
            "client_id" => $this->client_id,
            "name" => $this->name,
            "name_e" => $this->name_e,
            "url" => $this->url,
            "logo" => $this->PhotoUrl($this->logo),
            "address" => $this->address,
            "phone" => $this->phone,
            "cr" => $this->cr,
            "tax_id" => $this->tax_id,
            "vat_no" => $this->vat_no,
            "email" => $this->email,
            "website" => $this->website,
            "is_active" => $this->is_active == 1 ? "Active" : "not Active",
        ];
    }
}
