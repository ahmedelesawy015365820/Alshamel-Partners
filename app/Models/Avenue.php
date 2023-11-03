<?php

namespace App\Models;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Avenue extends Model
{
    use HasFactory, SoftDeletes, LogTrait;
    protected $table = 'general_avenues';

    protected $fillable = [
        'name',
        'name_e',
        'is_active',
        'country_id',
        'governorate_id',
        'city_id',
        "company_id",
    ];

    public function scopeData($query)
    {
        return $query->select('id', 'name',
            'name_e',
            'is_active',
            'country_id',
            'governorate_id',
            'city_id'
        )->with(['country:id,name,name_e', 'governorate:id,name,name_e', 'city:id,name,name_e']);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function governorate()
    {
        return $this->belongsTo(Governorate::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function customerBranches()
    {
        return $this->hasMany(\App\Models\CustomerBranch::class);
    }
    public function streets()
    {
        return $this->hasMany(Street::class);
    }

    // public function hasChildren()
    // {
    //     // $h = $this->internalSalesman()->exists();
    //     // return $h;

    //     return $this->customerBranches()->count() > 0 ||
    //     $this->streets()->count() > 0;
    // }

    public function hasChildren()
    {
        $relationsWithChildren = [];

        if ($this->customerBranches()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'customerBranches',
                'count' => $this->customerBranches()->count(),
                'ids' => $this->customerBranches()->pluck('id')->toArray(),
            ];
        }
        if ($this->streets()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'streets',
                'count' => $this->streets()->count(),
                'ids' => $this->streets()->pluck('id')->toArray(),
            ];
        }

        return $relationsWithChildren;
    }

    public function getActivitylogOptions(): \Spatie\Activitylog\LogOptions
    {
        $user = @auth()->user()->id ?: "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Avenue')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }

}
