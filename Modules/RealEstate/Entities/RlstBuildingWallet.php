<?php

namespace Modules\RealEstate\Entities;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Modules\RealEstate\Entities\RlstBuilding;
use Modules\RealEstate\Entities\RlstWallet;
use Illuminate\Database\Eloquent\Builder;

class RlstBuildingWallet extends Model
{
    use HasFactory, LogTrait;

    protected $table = 'rlst_building_wallet';
    protected $guarded = ['id'];
        

    
    public function scopeWallet($query)
    {
        /*
        return $query
            ->select('id','wallet_id','building_id','building_type_id')
            ->with('wallet:id,name,name_e', 'building:id,name,name_e');
        */
            return $query
            ->select('id', 'wallet_id', 'building_id', 'building_type_id')
            ->with(['wallet' => function ($query) {
            $query->select('id', 'name', 'name_e');
        }], 'building:id,name,name_e');
    }
    
    //public function 

    public function wallet()
    {
        return $this->belongsTo(RlstWallet::class);
    }

    public function building()
    {
        return $this->belongsTo(RlstBuilding::class, 'building_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Real Estate  Building Wallet')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }
}
