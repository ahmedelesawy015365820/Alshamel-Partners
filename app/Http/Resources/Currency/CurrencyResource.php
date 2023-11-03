<?php

namespace App\Http\Resources\Currency;

use Illuminate\Http\Resources\Json\JsonResource;

class CurrencyResource extends JsonResource
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
            'name' => $this->name,
            'name_e' => $this->name_e,
            'code' => $this->code,
            'code_e' => $this->code_e,
            'fraction' => $this->fraction,
            'fraction_e' => $this->fraction_e,
            'fraction_no' => $this->fraction_no,
            'symbol' => $this->symbol,
            'symbol_e' => $this->symbol_e,
            'is_default' => $this->is_default,
            'is_active' => $this->is_active,
        ];
    }
}
