<?php

namespace App\Models;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;

class ExternalSalesmen extends Model
{
    use HasFactory, SoftDeletes, LogTrait   ;
    protected $table = 'general_external_salesmen';

    protected $fillable = [
        'phone',
        'address',
        'rp_code',
        'email',
        'is_active',
        'national_id',
        'country_id',
        "company_id",
        "name",
        "name_e"
    ];


    public function scopeData($query)
    {
        return $query
            ->select('id','name','name_e','phone','address','rp_code','email','is_active','national_id','country_id')
            ->with('country:id,name,name_e');
    }


    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('External Salesmen')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }

}
