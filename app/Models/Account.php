<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LogTrait;
use Spatie\Activitylog\LogOptions;

class Account extends Model
{
    use HasFactory, LogTrait;
    protected $table = 'general_accounts';

    protected $guarded = ['id'];

    public function scopeData($query)
    {
        return $query
            ->select(
                'id',
                'name',
                'name_e',
                'account_number',
            );
    }

    public function buildingAccruedRevenues()
    {
        return $this->hasMany(\Modules\RealEstate\Entities\RlstBuilding::class, 'accrued_revenues_account_id');

    }

    public function buildingAdvanceRevenues()
    {
        return $this->hasMany(\Modules\RealEstate\Entities\RlstBuilding::class, 'advance_revenues_account_id');
    }

    public function buildingDiscounts()
    {
        return $this->hasMany(\Modules\RealEstate\Entities\RlstBuilding::class, 'discounts_account_id');
    }

    public function buildingCash()
    {
        return $this->hasMany(\Modules\RealEstate\Entities\RlstBuilding::class, 'cash_account_id');
    }

    public function buildingKnet()
    {
        return $this->hasMany(\Modules\RealEstate\Entities\RlstBuilding::class, 'knet_account_id');
    }

    public function buildingInsurance()
    {
        return $this->hasMany(\Modules\RealEstate\Entities\RlstBuilding::class, 'insurance_account_id');
    }


    public function hasChildren()
    {
        $relationsWithChildren = [];

        if ($this->buildingAccruedRevenues()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'buildingAccruedRevenues',
                'count' => $this->buildingAccruedRevenues()->count(),
                'ids' => $this->buildingAccruedRevenues()->pluck('rlst_buildings.id')->toArray(),
            ];
        }


        if ($this->buildingAdvanceRevenues()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'buildingAdvanceRevenues',
                'count' => $this->buildingAdvanceRevenues()->count(),
                'ids' => $this->buildingAdvanceRevenues()->pluck('rlst_buildings.id')->toArray(),
            ];
        }

        if ($this->buildingDiscounts()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'buildingDiscounts',
                'count' => $this->buildingDiscounts()->count(),
                'ids' => $this->buildingDiscounts()->pluck('rlst_buildings.id')->toArray(),
            ];
        }

        if ($this->buildingCash()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'buildingCash',
                'count' => $this->buildingCash()->count(),
                'ids' => $this->buildingCash()->pluck('rlst_buildings.id')->toArray(),
            ];
        }

        if ($this->buildingKnet()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'buildingKnet',
                'count' => $this->buildingKnet()->count(),
                'ids' => $this->buildingKnet()->pluck('rlst_buildings.id')->toArray(),
            ];
        }

        if ($this->buildingInsurance()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'buildingInsurance',
                'count' => $this->buildingInsurance()->count(),
                'ids' => $this->buildingInsurance()->pluck('rlst_buildings.id')->toArray(),
            ];
        }

        return $relationsWithChildren;

    } // hasChildren()



    public function getActivitylogOptions(): \Spatie\Activitylog\LogOptions
    {
        $user = @auth()->user()->id ?: "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('General Account')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }

}
