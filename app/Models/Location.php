<?php

namespace App\Models;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;

class Location extends Model
{
    use HasFactory, LogTrait;

    protected $table = 'general_locations';

    protected $fillable = [
        'id',
        'name',
        'name_e',
        'parent_id',
        'priority_id',
        'company_id',
    ];

    public function scopeData($query)
    {
        return $query->select(
            'id',
            'name',
            'name_e',
            "priority_id",
            'parent_id',
        )->with(
            'parent:id,name,name_e',
            'priority:id,name,name_e',

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
        return $this->belongsTo(Location::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Location::class, 'parent_id');
    }

    public function hasChildren()
    {
        return $this->children()->count() > 0;
    }

    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Location')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }

    public function priority()
    {
        return $this->belongsTo(Priority::class, 'priority_id');
    }
}
