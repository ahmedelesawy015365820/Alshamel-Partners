<?php

namespace Modules\RealEstate\Entities;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;

class RlstWallet extends Model
{
    use HasFactory, LogTrait, SoftDeletes;

    protected $guarded = ['id'];

    protected $table = 'rlst_wallets';


    public function scopeData($query)
    {
        return $query->select('id', 'name', 'name_e')
            ->with([
                'owners:id,name,name_e',
                'buildings:id,name,name_e',
            ])
            ->whereNull('deleted_at');
    }

    // relations
    public function owners()
    {
        return $this->belongsToMany(\Modules\RealEstate\Entities\RlstOwner::class, 'rlst_wallet_owners', 'wallet_id', 'owner_id')->withPivot('percentage')->wherePivotNull('deleted_at');

    }

    public function walletOwner()
    {
        return $this->hasMany(\Modules\RealEstate\Entities\RlstWalletOwner::class, 'wallet_id')->whereNull('deleted_at');
    }

    public function buildingWallet()
    {
        return $this->hasMany(\Modules\RealEstate\Entities\RlstBuildingWallet::class, 'wallet_id')->whereNull('deleted_at');
    }

    public function buildings()
    {
        return $this->belongsToMany(\Modules\RealEstate\Entities\RlstBuilding::class, 'rlst_building_wallet', 'wallet_id', 'building_id')->withPivot('building_type_id')->wherePivotNull('deleted_at');

    }

    public function hasChildren()
    {
        $relationsWithChildren = [];

        if ($this->walletOwner()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'walletOwner',
                'count' => $this->walletOwner()->count(),
                'ids' => $this->walletOwner()->pluck('id')->toArray(),
            ];
        }

        if ($this->buildingWallet()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'buildingWallet',
                'count' => $this->buildingWallet()->count(),
                'ids' => $this->buildingWallet()->pluck('id')->toArray(),
            ];
        }

        return $relationsWithChildren;
    }

    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Real Estate Wallets')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }

}
