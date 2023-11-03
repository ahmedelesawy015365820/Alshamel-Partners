<?php

namespace App\Models;

use App\Models\GeneralCustomer;
use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerBranch extends Model
{
    use HasFactory, LogTrait;

    protected $table = "general_customer_branches";


    protected $fillable = [
        'name',
        'name_e',
        'country_id',
        'city_id',
        'governorate_id',
        'avenue_id',
        'street_id',
        'latitude',
        'longitude',
    ];


    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function governorate()
    {
        return $this->belongsTo(Governorate::class);
    }

    public function avenue()
    {
        return $this->belongsTo(Avenue::class);
    }

    public function street()
    {
        return $this->belongsTo(Street::class);
    }

    public function customer()
    {
        return $this->belongsTo(GeneralCustomer::class);
    }

}
