<?php

namespace Modules\RealEstate\Entities;

use App\Models\Street;
use App\Models\Avenue;
use App\Models\City;
use App\Models\Country;
use App\Models\Governorate;
use App\Traits\LogTrait;
use App\Traits\MediaTrait;
use App\Traits\VideoLink;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Modules\RealEstate\Entities\RlstBuildingType;
use App\Models\Currency;
use App\Models\GeneralMainCostCenters;
use App\Models\Account;
class RlstBuilding extends Model implements HasMedia
{
    use HasFactory, LogTrait, MediaTrait, VideoLink;

    protected $guarded = ['id'];

    public function scopeData($query)
    {
        return $query
        /*
            ->select('id',
                'name',
                'name_e',
                'description',
                'description_e',
                'land_area',
                'building_area',
                'construction_year',
                'project_id',
                'country_id',
                'city_id',
                'avenue_id',
                'lng',
                'lat',
                'properties',
                'attachments',
                'module')
        */        
            ->with(
                    'buildingType:id,name,name_e',
                    'buildingCurrency:id,name,name_e',
                    'mainCostCenter:id,name,name_e',

                    'country:id,name,name_e',
                    'governorate:id,name,name_e',
                    'city:id,name,name_e',
                    'avenue:id,name,name_e',
                    'street:id,name,name_e',
                    'wallets:id,name,name_e',
                    'policies:id,name,name_e,description',
                )
            ->whereNull('deleted_at');    
                
    }

    // attributes
    public function setPropertiesAttribute($value)
    {
        $this->attributes['properties'] = json_encode($value);
    }
    public function getPropertiesAttribute($value)
    {
        return json_decode($value);
    }

    public function setAttachmentsAttribute($value)
    {
        $this->attributes['attachments'] = json_encode($value);
    }

    public function getAttachmentsAttribute($value)
    {
        return json_decode($value);
    }

    // relations
    public function buildingWallet()
    {
        return $this->hasMany(\Modules\RealEstate\Entities\RlstBuildingWallet::class, 'building_id')->whereNull('deleted_at');
    }

    public function wallets()
    {
        return $this->belongsToMany(\Modules\RealEstate\Entities\RlstWallet::class, 'rlst_building_wallet', 'building_id', 'wallet_id')->wherePivotNull('deleted_at');
    }

    public function buildingPolicies()
    {
        return $this->hasMany(\Modules\RealEstate\Entities\RlstBuildingPolicy::class, 'building_id');
    }

    public function policies()
    {
        return $this->belongsToMany(\Modules\RealEstate\Entities\RlstPolicy::class, 'rlst_building_policies', 'building_id', 'policy_id');
    }


    public function module()
    {
        return $this->belongsTo(\App\Models\Module::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function governorate()
    {
        return $this->belongsTo(Governorate::class, 'governorate_id');
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function avenue()
    {
        return $this->belongsTo(Avenue::class, 'avenue_id');
    }

    public function street()
    {
        return $this->belongsTo(Street::class, 'street_id');
    }

    public function owners()
    {
        $walletIds = $this->wallets()->pluck('rlst_building_wallet.wallet_id');

        return \Modules\RealEstate\Entities\RlstOwner::whereHas('wallets', function ($query) use ($walletIds) {
            $query->whereIn('wallet_id', $walletIds);
        })->get();
    }



    
    public function account($accountId)
    {
        return $accountId ? Account::find($accountId)->only(['id','name','name_e','account_number']) : null;
    }
    

    public function buildingType()
    {
        return $this->belongsTo(RlstBuildingType::class, 'building_type_id');
    }

    public function buildingCurrency()
    {
        return $this->belongsTo(Currency::class, 'building_currency_id');
    }

    public function mainCostCenter()
    {
        return $this->belongsTo(GeneralMainCostCenters::class, 'main_cost_center_id');
    }


    public function hasChildren()
    {
        $relationsWithChildren = [];

        if ($this->buildingWallet()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'buildingWallet',
                'count' => $this->buildingWallet()->count(),
                'ids' => $this->buildingWallet()->pluck('id')->toArray(),
            ];
        }

        return $relationsWithChildren;
    }


}
