<?php

namespace Modules\ClubMembers\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\ClubMembers\Entities\CmMemberPermission;
use Modules\ClubMembers\Entities\CmTypePermission;

class CmTypePermissionResource extends JsonResource
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
            "id" => $this->id,
            "type" => new CmMemberTypeResource($this->type),
            "permission" => new CmMemberPermissionResource($this->permission),
            "financialStatus" => new CmFinancialStatusResource($this->financialStatus),
            "membership_period" => $this->membership_period,
            "allowed_subscription_date" => $this->allowed_subscription_date,
            "allowed_vote_date" => $this->allowed_vote_date,
            "cm_permissions_id" => $this->cm_permissions_id,

        ];


    }

    // public function permissions ($permissions_id)
    // {
    //     $data = [];
    //     foreach ($permissions_id as $id)
    //     {
    //         $data[]=CmMemberPermission::find($id);
    //     }
    //     return $data;
    // }
}
