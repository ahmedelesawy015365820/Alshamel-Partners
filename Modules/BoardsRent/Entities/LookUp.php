<?php

namespace Modules\BoardsRent\Entities;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;

class LookUp extends Model
{
    use HasFactory, LogTrait;

    protected $table = 'boards_rent_look_ups';
    protected $fillable = [
        'parent_id', 'name', 'name_e', 'type','company_id'
    ];
    protected $appends = ['haveChildren'];


    // is parent attribute

    public function getHaveChildrenAttribute()
    {
        return static::where("parent_id", $this->id)->count() > 0;
    }

    public function children()
    {
        return $this->hasMany(LookUp::class, 'parent_id');
    }

    public function hasChildren()
    {
        $h = $this->children()->count() > 0;
        return $h;
    }

    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('LookUp')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }
}
