<?php

namespace Modules\RealEstate\Entities;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RlstContractDetail extends Model
{
    use HasFactory, SoftDeletes, LogTrait;

    protected $fillable = [
        "building_id",
        "unit_id",
        "installment_payment_plans_id",
        "contract_id",
        "reservation_value",
        "unit_value",
    ];

    // relations

    public function building()
    {
        return $this->belongsTo(\Modules\RealEstate\Entities\RlstBuilding::class, 'building_id', 'id');
    }

    public function unit()
    {
        return $this->belongsTo(\Modules\RealEstate\Entities\RlstUnit::class, 'unit_id', 'id');
    }

    public function planInstallment()
    {
        return $this->belongsTo(\Modules\RecievablePayable\Entities\RpInstallmentPaymentPlan::class, 'installment_payment_plans_id', 'id');
    }

    public function contract()
    {
        return $this->belongsTo(\Modules\RealEstate\Entities\RlstContract::class, 'contract_id', 'id');
    }

}
