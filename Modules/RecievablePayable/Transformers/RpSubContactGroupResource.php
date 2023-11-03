<?php

namespace Modules\RecievablePayable\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class RpSubContactGroupResource extends JsonResource
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
            "id"=>$this->id,
            "name"=>$this->name,
            "name_e"=>$this->name_e,
            "gl_acc_no"=>$this->gl_acc_no,
            "rp_main_contact_group_id"=>$this->rp_main_contact_group_id,
            "mainContactGroup"=>$this->mainContactGroup,
            "glAccount"=>$this->glAccount
        ];
    }
}
