<?php

namespace App\Models;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\BoardsRent\Entities\Panel;

class City extends Model
{
    use HasFactory, LogTrait;
    protected $table = 'general_cities';

    protected $guarded = ['id'];

    public function scopeData($query)
    {
        return $query
            ->select('id',
                'name',
                'name_e',
                'is_active',
                'country_id', 'governorate_id')
            ->with('country:id,name,name_e', 'governorate:id,name,name_e');
    }

    public function governorate()
    {
        return $this->belongsTo(Governorate::class);
    }

    public function rlstOwners()
    {
        return $this->hasMany(\Modules\RealEstate\Entities\RlstOwner::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function avenues()
    {
        return $this->hasMany(Avenue::class);
    }
    public function customerBranches()
    {
        return $this->hasMany(\App\Models\CustomerBranch::class);
    }

    public function Panels()
    {
        return $this->hasMany(Panel::class, 'city_id');
    }

    public function hasChildren()
    {
        return $this->customerBranches()->count() > 0 ||
            $this->avenues()->count() > 0 ||
            $this->Panels()->count() > 0 ||
            $this->rlstOwners()->count() > 0
            ;

    }

    // public function hasChildren()
    // {
    //     $relationsWithChildren = [];

    //     if ($this->avenues()->count() > 0) {
    //         $relationsWithChildren[] = [
    //             'relation' => 'avenues',
    //             'count' => $this->avenues()->count(),
    //             'ids' => $this->avenues()->pluck('id')->toArray(),
    //         ];
    //     }
    //     if ($this->customerBranches()->count() > 0) {
    //         $relationsWithChildren[] = [
    //             'relation' => 'customerBranches',
    //             'count' => $this->customerBranches()->count(),
    //             'ids' => $this->customerBranches()->pluck('id')->toArray(),
    //         ];
    //     }

    //     if ($this->rlstOwners()->count() > 0) {
    //         $relationsWithChildren[] = [
    //             'relation' => 'rlstOwners',
    //             'count' => $this->rlstOwners()->count(),
    //             'ids' => $this->rlstOwners()->pluck('id')->toArray(),
    //         ];
    //     }
    //     if ($this->Panels()->count() > 0) {
    //         $relationsWithChildren[] = [
    //             'relation' => 'Panels',
    //             'count' => $this->Panels()->count(),
    //             'ids' => $this->Panels()->pluck('id')->toArray(),
    //         ];
    //     }

    //     return $relationsWithChildren;
    // }

    public function getActivitylogOptions(): \Spatie\Activitylog\LogOptions
    {
        $user = @auth()->user()->id ?: "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('City')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }

}
