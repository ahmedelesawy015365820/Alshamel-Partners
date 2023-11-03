<?php

namespace Modules\ClubMembers\Transformers;

use App\Http\Resources\Status\StatusResource;
use Illuminate\Http\Resources\Json\JsonResource;

class CmMemberSettingResource extends JsonResource
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
            'member_type_id' => $this->member_type_id,
            'financial_status_id' => $this->financial_status_id,
            'general_status_id' => $this->general_status_id,
            'date' => $this->date,
            'member-type' => $this->memberType,
            'financial-status' => $this->financialStatus,
            "status" => new StatusResource($this->generalStatus),
        ];
    }
}
