<?php

namespace App\Models;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Contracts\Activity;
use Spatie\Activitylog\LogOptions;

class Unit extends Model
{
    use HasFactory, SoftDeletes, LogTrait   ;

    protected $table = 'general_units';
    protected $fillable = ['name', 'name_e', 'is_active','company_id'];

    public function scopeData($query)
    {
        return $query->select('id','name', 'name_e','is_active');
    }

    // protected $casts = [
    //     'is_active' => '\App\Enums\IsActive',
    // ];

    public function products()
    {
        return $this->hasMany(Product::class, 'unit_id');
    }

    public function hasChildren()
    {
        $relationsWithChildren = [];

        if ($this->products()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'products',
                'count' => $this->products()->count(),
                'ids' => $this->products()->pluck('id')->toArray()
            ];
        }

        return $relationsWithChildren;
    }

    public function tapActivity(Activity $activity, string $eventName)
    {
        $activity->causer_id = auth()->user()->id ?? 0;
        $activity->causer_type = auth()->user()->role ?? "admin";
    }

    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Unit')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }

}
