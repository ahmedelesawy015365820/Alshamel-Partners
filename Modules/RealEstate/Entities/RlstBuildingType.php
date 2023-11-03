<?php

namespace Modules\RealEstate\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\LogTrait;


class RlstBuildingType extends Model
{
    use HasFactory, LogTrait;

    protected $table = 'rlst_building_types';

    protected $guarded = ['id'];
    
    protected function buildings()
    {
        return $this->hasMany(\Modules\RealEstate\Entities\RlstBuilding::class, 'building_type_id');
    }

    public function hasChildren()
    {
        $relationsWithChildren = [];

        if ($this->buildings()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'buildings',
                'count' => $this->buildings()->count(),
                'ids' => $this->buildings()->pluck('rlst_buildings.id')->toArray(),
            ];
        }

        return $relationsWithChildren;

    }
    
    
    public function getActivitylogOptions(): \Spatie\Activitylog\LogOptions
    {
        $user = @auth()->user()->id ?: "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('BuildingType')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }

}
