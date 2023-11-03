<?php

namespace Modules\ClubMembers\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class CmMembershipRenewalResource extends JsonResource
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
            'from' => $this->from,
            'to' => $this->to,
            'membership_availability' => $this->membership_availability,
            'membership_cost' => $this->membership_cost,
            'renewal_availability' => $this->renewal_availability,
            'renewal_cost' => $this->renewal_cost,
        ];
    }
}
