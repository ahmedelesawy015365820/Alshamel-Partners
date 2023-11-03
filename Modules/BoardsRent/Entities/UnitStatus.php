<?php

namespace Modules\BoardsRent\Entities;

use App\Models\Status;
use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;

class UnitStatus extends Model
{
    use HasFactory, LogTrait;

    protected $fillable = [
        'id',
        'name',
        'name_e',
        'status_id',
        'company_id',
    ];

    protected $table = 'boards_rent_unit_statuses';


    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id');
    }

    public function Panels()
    {
        return $this->hasMany(Panel::class, 'unit_status_id');
    }

    public function hasChildren()
    {
        return $this->statuses()->count() > 0 ||
        $this->panels()->count() > 0;
    }

    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('UnitStatus')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }
}
