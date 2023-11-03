<?php

namespace App\Models;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;

class Equipment extends Model
{
    use HasFactory, LogTrait;

    protected $table = 'general_equipments';

    protected $guarded = ['id'];

    public function scopeData($query)
    {
        return $query->select(
                'id', 'name', 'name_e', 'parent_id', 'location_id', 'periodic_maintenance_id'
            )->with(
                'parent:id,name,name_e',
                'location:id,name,name_e',
                'periodicMaintenance:id,name,name_e',
            );
    }

    protected $appends = ['haveChildren'];

    // relations

    public function getHaveChildrenAttribute()
    {
        return static::where("parent_id", $this->id)->count() > 0;
    }

    public function parent()
    {
        return $this->belongsTo(Equipment::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Equipment::class, 'parent_id');
    }

    public function hasChildren()
    {
        return $this->children()->count() > 0;
    }

    public function periodicMaintenance()
    {
        return $this->belongsTo(PeriodicMaintenance::class, 'periodic_maintenance_id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Equipment')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }
}
