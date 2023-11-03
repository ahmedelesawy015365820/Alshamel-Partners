<?php

namespace Modules\RealEstate\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\RealEstate\Transformers\RlstBuildingResource;
use Modules\RealEstate\Transformers\RlstPolicyResource;
use Modules\RealEstate\Entities\RlstBuilding;
use Modules\RealEstate\Entities\RlstPolicy;



class RlstBuildingPolicyResource extends JsonResource
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
        'building' => $this->whenLoaded('building'),
        //RlstBuilding::find($this->building_id)->only(['id','name','name_e']),
        'year' => $this->year,
        'month' => $this->month,
        'policy' => $this->whenLoaded('policy'),
        //RlstPolicy::find($this->policy_id),
        'amount' => $this->amount,
        'percent' => $this->percent,
        'percent_amount' => $this->percent_amount,
        'after_expenses' => $this->after_expenses,  
        'collected_rent_type' => $this->collected_rent_type,
        'plus_extra_revenues' => $this->plus_extra_revenues, 
        'company_pays_rest' => $this->company_pays_rest,
        'owner_pays_rest' => $this->owner_pays_rest,                   
        ];
    }
}
