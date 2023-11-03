<?php

namespace Modules\ClubMembers\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\ClubMembers\Entities\CmMember;
use Illuminate\Support\Facades\DB;
use Modules\ClubMembers\Transformers\CmMemberResource;


class CmSponsorPaidTransactionsResource extends JsonResource 
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request){

        return 
        [
            'id'            => $this->id,   
            "full_name"     => collect($this->member)->only(['full_name'])->first(),
            'member_number' => $this->member_number,
            'serial_id'     => $this->serial_id,
            'document_no'   => $this->document_no,
            'date'          => $this->date,
            'amount'        => $this->amount,          
            'year'          => $this->year,
            'type'          => $this->type
            
        ];
            

    }
    
    
}
