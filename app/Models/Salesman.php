<?php

namespace App\Models;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\RealEstate\Entities\RlstReservation;
use Spatie\Activitylog\LogOptions;

class Salesman extends Model
{
    use HasFactory, SoftDeletes, LogTrait;

    protected $fillable = [
        'name',
        'name_e',
        'salesman_type_id',
        "company_id",
    ];

    public function scopeData($query)
    {
        return $query->select(
            'name',
            'name_e',
            'salesman_type_id',
        )->with(['salesmanType:id,name,name_e']);
    }

    protected $table = "general_salesmen";

    // relations
    public function salesmanType()
    {
        return $this->belongsTo(SalesmenType::class, 'salesman_type_id');
    }

    public function reservations()
    {
        return $this->hasMany(RlstReservation::class);
    }

    public function orders()
    {
        return $this->hasMany(\Modules\BoardsRent\Entities\Order::class);
    }

    public function hasChildren()
    {
        return $this->reservations()->count() > 0 ||
        $this->orders()->count() > 0;
    }

    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Salesman')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }

}
