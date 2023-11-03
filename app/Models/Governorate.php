<?php

namespace App\Models;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use Modules\BoardsRent\Entities\OrderDetails;
use Modules\BoardsRent\Entities\Panel;

class Governorate extends Model
{
    use HasFactory, LogTrait;
    protected $table = 'general_governorates';
    protected $guarded = ['id'];

    public function scopeData($query)
    {
        return $query
            ->select('id',
                'name',
                'name_e',
                'is_default',
                'is_active',
                'phone_key',
                'country_id')
            ->with('country:id,name,name_e');
    }

    public function country()
    {
        return $this->belongsTo(Country::class, "country_id");
    }

    public function cities()
    {
        return $this->hasMany(City::class);
    }

    public function avenues()
    {
        return $this->hasMany(Avenue::class);
    }
    public function customerBranches()
    {
        return $this->hasMany(\App\Models\CustomerBranch::class);
    }

    public function panels()
    {
        return $this->hasMany(Panel::class, 'governorate_id');
    }

    public function orderDetails()
    {
        return $this->hasMany(\Modules\BoardsRent\Entities\OrderDetails::class);
    }

    // public function hasChildren()
    // {
    //     return $this->avenues()->count() > 0 ||
    //     $this->cities()->count() > 0 ||
    //     $this->panels()->count() > 0 ||
    //     $this->customerBranches()->count() > 0 ||
    //     $this->orderDetails()->count() > 0;

    // }

    public function hasChildren()
    {
        $relationsWithChildren = [];

        if ($this->avenues()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'avenues',
                'count' => $this->avenues()->count(),
                'ids' => $this->avenues()->pluck('id')->toArray(),
            ];
        }
        if ($this->cities()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'cities',
                'count' => $this->cities()->count(),
                'ids' => $this->cities()->pluck('id')->toArray(),
            ];
        }

        if ($this->panels()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'panels',
                'count' => $this->panels()->count(),
                'ids' => $this->panels()->pluck('id')->toArray(),
            ];
        }
        if ($this->customerBranches()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'customerBranches',
                'count' => $this->customerBranches()->count(),
                'ids' => $this->customerBranches()->pluck('id')->toArray(),
            ];
        }
        if ($this->orderDetails()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'orderDetails',
                'count' => $this->orderDetails()->count(),
                'ids' => $this->orderDetails()->pluck('id')->toArray(),
            ];
        }

        return $relationsWithChildren;
    }

    public function getActivitylogOptions(): \Spatie\Activitylog\LogOptions
    {
        $user = @auth()->user()->id ?: "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Governorate')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }
}
