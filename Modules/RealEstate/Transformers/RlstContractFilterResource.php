<?php

namespace Modules\RealEstate\Transformers;

use App\Http\Resources\Branch\BranchResource;
use App\Http\Resources\Document\DocumentResource;
use App\Http\Resources\GeneralCustomer\GeneralCustomerResource;
use App\Http\Resources\Salesman\SalesmanResource;
use App\Http\Resources\Serials\SerialResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\RealEstate\Entities\RlstBuilding;
use Modules\RealEstate\Entities\RlstBuildingWallet;
use Modules\RealEstate\Entities\RlstContractDetail;
use Modules\RealEstate\Entities\RlstOwner;
use Modules\RealEstate\Entities\RlstUnit;
use Modules\RealEstate\Entities\RlstWalletOwner;

class RlstContractFilterResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {

        $contractDetails = RlstContractDetail::where('id', $this->id)->first();
        $buildingWallet = RlstBuildingWallet::where('building_id', $this->building_id)->first();
        $ownerId = RlstWalletOwner::where('wallet_id', $buildingWallet->wallet_id)->first();
        return [
            'id' => $this->contract->id,
            "date" => $this->contract->date,
            "start_date" => $this->contract->start_date,
            "end_date" => $this->contract->end_date,
            "serial_number" => $this->contract->serial_number,
            "prefix" => $this->contract->prefix,
            "salesman" => new SalesmanResource($this->contract->salesman),
            "customer" => new GeneralCustomerResource($this->contract->customer),
            "branch" => new BranchResource($this->contract->branch),
            "document" => new DocumentResource($this->contract->document),
            "serial" => new SerialResource($this->contract->serial),
            'contract_details' => new RlstContractDetailsResource($contractDetails),
            'building' => new RlstBuildingResource(RlstBuilding::find(($contractDetails->building_id))),
            'unit' => new RlstUnitResource(RlstUnit::find($contractDetails->unit_id)),
            'owner' => new RlstOwnerResource(RlstOwner::find($ownerId->owner_id)),
        ];

    }
}
