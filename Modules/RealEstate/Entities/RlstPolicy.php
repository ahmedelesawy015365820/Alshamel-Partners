<?php

namespace Modules\RealEstate\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\LogTrait;

class RlstPolicy extends Model
{
    use HasFactory, LogTrait;

    protected $table = 'rlst_policies';

    protected $guarded = ['id'];


    public function scopeData($query)
    {
        return $query->select('id', 'name', 'name_e', 'description')
            ->with([
                'buildings:id,name,name_e',
            ]);
            
    }


    // relations

    public function buildings()
    {
        return $this->belongsToMany(\Modules\RealEstate\Entities\RlstBuilding::class, 'rlst_building_policies', 'policy_id', 'building_id')
        ->withPivot(['year','month','amount','percent','percent_amount','after_expenses','plus_extra_revenues',
                    'collected_rent_type','company_pays_rest','owner_pays_rest'
                    ]);

    }

    public function buildingPolicy()
    {
        return $this->hasMany(\Modules\RealEstate\Entities\RlstBuildingPolicy::class, 'policy_id');
    }

    public function hasChildren()
    {
        $relationsWithChildren = [];

        if ($this->buildingPolicy()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'buildingPolicy',
                'count' => $this->buildingPolicy()->count(),
                'ids' => $this->buildingPolicy()->pluck('id')->toArray(),
            ];
        }
        return $relationsWithChildren;
    }

    public function getActivitylogOptions(): \Spatie\Activitylog\LogOptions
    {
        $user = @auth()->user()->id ?: "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Policy')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }

}
