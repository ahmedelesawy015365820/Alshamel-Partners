<?php

namespace App\Models;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;

class GeneralCustomerSource extends Model
{
    use HasFactory, LogTrait;

    protected $table = 'general_customer_sources';

    protected $fillable = [
        'name',
        'name_e',
        'parent_id',
    ];

    public function scopeData($query)
    {
        return $query->select(
            'id', 'name', 'name_e', 'parent_id'
        )->with(
            'parent:id,name,name_e',

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
        return $this->belongsTo(GeneralCustomerSource::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(GeneralCustomerSource::class, 'parent_id');
    }

    //  public function hasChildren()
    //  {
    //      return $this->children()->count() > 0;
    //  }

    public function hasChildren()
    {
        $relationsWithChildren = [];

        if ($this->children()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'general customer sources',
                'count' => $this->children()->count(),
                'ids' => $this->children()->pluck('id')->toArray(),
            ];
        }
        return $relationsWithChildren;
    }

    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('General Customer Source')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }
}
