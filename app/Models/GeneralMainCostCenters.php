<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LogTrait;
use Spatie\Activitylog\LogOptions;

class GeneralMainCostCenters extends Model
{
    use HasFactory, LogTrait;
    protected $table = 'general_main_cost_centers';

    protected $guarded = ['id'];

    public function scopeData($query)
    {
        return $query
            ->select(
                'id',
                'name',
                'name_e',
            );
    }


    public function buildings()
    {
        return $this->hasMany(\Modules\RealEstate\Entities\RlstBuilding::class, 'main_cost_center_id');
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
            ->useLogName('General Main Cost Centers')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }

}
