<?php

namespace App\Models;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendant extends Model
{
    use HasFactory, LogTrait;

    protected $table = 'general_attendants';

    protected $guarded = ['id'];



    public function customer()
    {
        return $this->belongsTo(GeneralCustomer::class, 'customer_id');
    }

    public function scopeData($query)
    {
        return $query
            ->select('id','name','name_e','national_id','id_number','notes','customer_id')
            ->with('customer:id,name,name_e');
    }

    public function getActivitylogOptions(): \Spatie\Activitylog\LogOptions
    {
        $user = @auth()->user()->id ?: "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Variant Attribute')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }
}
