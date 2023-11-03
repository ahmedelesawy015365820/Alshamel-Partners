<?php

namespace Modules\ClubMembers\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class GetSponsorMembersResource extends JsonResource
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
            'first_name' => $this->first_name,
            'second_name' => $this->second_name,
            'third_name' => $this->third_name,
            'last_name' => $this->last_name,
            'family_name' => $this->family_name,
            'full_name' => $this->full_name,
            'membership_number' => $this->membership_number,
            'national_id' => $this->national_id,
            'national_no' => $this->national_no,
            'home_phone' => $this->home_phone,
            'sponsor_id' => $this->sponsor_id,

        ];
    }
}
