<?php

namespace Modules\RealEstate\Transformers;

use App\Http\Resources\AllDropListResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\RealEstate\Entities\RlstBuildingWallet;
use Modules\RealEstate\Entities\RlstOwner;
use Modules\RealEstate\Entities\RlstWallet;
use Modules\RealEstate\Entities\RlstWalletOwner;

class RlstUnSoldUnitReportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $buildingWallet = RlstBuildingWallet::where('building_id', $this->building_id)->first();
        if ($buildingWallet) {
            $ownerId = RlstWalletOwner::where('wallet_id', $buildingWallet->wallet_id)->first();
        }

        return [
            'id' => $this->id,

            'name' => $this->name,
            'name_e' => $this->name_e,
            'building_id' => $this->building_id,
            'building' => new AllDropListResource($this->building),

            'unit_ty' => $this->type,
            'unit_area' => $this->unit_area,

            'rooms' => $this->rooms,

            'country' => new AllDropListResource($this->building->country),
            'city' => new AllDropListResource($this->building->city),
            'governorate' => new AllDropListResource($this->building->governorate),
            'path' => $this->path,

            'owner' =>  isset($ownerId) ? new RlstOwnerResource(RlstOwner::find($ownerId->owner_id)) : null,
            'wallet' => isset($ownerId) ?  new RlstWalletResource(RlstWallet::find($ownerId->wallet_id)): null,
        ];
    }
}
