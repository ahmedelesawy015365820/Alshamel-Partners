<?php

namespace Modules\BoardsRent\Entities;

use App\Models\Avenue;
use App\Models\City;
use App\Models\Country;
use App\Models\GeneralCustomer;
use App\Models\Salesman;
use Modules\BoardsRent\Entities\CustomerSource;
use Modules\BoardsRent\Entities\Sector;
use Spatie\Activitylog\LogOptions;

class Customer extends GeneralCustomer
{
    protected $fillable = [
        "name",
        'name_e',
        "phone",
        'email',
        'country_id',
        'city_id',
        "avenue_id",
        'contact_person',
        "contact_phone",
        'whatsapp',
        'facebook',
        'instagram',
        'linkedin',
        'twitter',
        'snapchat',
        'salesman_id',
        'customer_source_id',
        "sector_id",
        "website",
        'type'
    ];

    public function avenue()
    {
        return $this->belongsTo(Avenue::class);
    }

    public function sector()
    {
        return $this->belongsTo(Sector::class);
    }

    public function customerSource()
    {
        return $this->belongsTo(CustomerSource::class);
    }

    public function salesman()
    {
        return $this->belongsTo(Salesman::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function orders()
    {
        return $this->hasMany(\Modules\PointOfSale\Entities\Order::class);
    }

    public function hasChildren()
    {
        return $this->orders->count();
    }

    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Customer')
            ->setDescriptionForEvent(fn (string $eventName) => "This model has been {$eventName} by ($user)");
    }
}
