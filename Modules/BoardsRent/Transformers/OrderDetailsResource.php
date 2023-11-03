<?php

namespace Modules\BoardsRent\Transformers;

use App\Http\Resources\CategoryResource;
use App\Http\Resources\Governorate\GovernorateResource;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $data = [
            'id' => $this->id,
            "category_id" => $this->category_id,
            'governorate_id' =>  $this->governorate_id,
            'package_id' => $this->package_id,
            "category" => $this->category ? new CategoryResource($this->category) : null,
            'governorate' => $this->governorate ? new GovernorateResource($this->governorate) : null,
            'package' => $this->package ? new PackageResource($this->package) : null,
            'panels' => $this->panels ?  PanelResource::collection($this->panels) : null,
            'quantity' => $this->quantity,
            'from' => $this->from,
            'to' => $this->to,
            'price' => $this->price,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
        return array_filter($data);
    }
}
