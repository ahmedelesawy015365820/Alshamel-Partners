<?php

namespace App\Http\Resources\Transaction;

use App\Http\Resources\Branch\BranchResource;
use App\Http\Resources\Branch\RelationBranchResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\ClubMembers\Transformers\CmSponserResource;
use Modules\RecievablePayable\Transformers\BreakDownResource;

class TransactionResource extends JsonResource
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
            'id' => $this->id,
            'date' => $this->date,
            'amount' => $this->amount,
            'module_type' => $this->module_type,
            'invoice_id' => $this->invoice_id,
            'break_id' => $this->break_id,
//            'invoice' =>  new RlstInvoiceResource($this->invoice),
            'break_down' => new BreakDownResource($this->breakDown),
            'branch' => new RelationBranchResource($this->branch),
            'sponsor' => new CmSponserResource($this->sponsor),
            "member" => $this->cm_member_id ? new \Modules\ClubMembers\Transformers\CmMemberResource($this->member) : null,
            'date_from' => $this->date_from,
            'date_to' => $this->date_to,
            'type' => $this->type,
            'prefix' => $this->prefix,
            'serial_number' => $this->serial_number,
            'number_of_years' =>$this->number_of_years
        ];
    }
}
