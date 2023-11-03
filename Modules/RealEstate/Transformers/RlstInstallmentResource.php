<?php

namespace Modules\RealEstate\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Currency\CurrencyResource;

class RlstInstallmentResource  extends JsonResource
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
            "date" => $this->date,
            "amount" => $this->amount,
            "currency" => new CurrencyResource($this->currency),
            "rest_amount" => $this->rest_amount,
            "pay_type" => $this->pay_type,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

        ];
    }
}
