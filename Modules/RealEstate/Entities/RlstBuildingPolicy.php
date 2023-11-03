<?php

namespace Modules\RealEstate\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\LogTrait;


class RlstBuildingPolicy extends Model
{
    use HasFactory, LogTrait;

    protected $table = 'rlst_building_policies';

    protected $guarded = ['id'];

    public function policy()
    {
        return $this->belongsTo(\Modules\RealEstate\Entities\RlstPolicy::class, 'policy_id');
    }

    public function building()
    {
        return $this->belongsTo(\Modules\RealEstate\Entities\RlstBuilding::class, 'building_id');
    }

    public function scopeData($query)
    {
        return $query->select('id', 'policy_id', 'building_id', 'year', 'month', 'amount', 'percent', 'percent_amount', 'after_expenses', 'plus_extra_revenues', 'collected_rent_type', 'company_pays_rest', 'owner_pays_rest')
            ->with([
                'policy:id,name,name_e',
                'building:id,name,name_e',
            ]);
    }



    public function getActivitylogOptions(): \Spatie\Activitylog\LogOptions
    {
        $user = @auth()->user()->id ?: "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Building Policy')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }
}
