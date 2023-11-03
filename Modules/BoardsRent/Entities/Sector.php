<?php

namespace Modules\BoardsRent\Entities;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;

class Sector extends Model
{
    use HasFactory, LogTrait;

    protected $fillable = [
        'id',
        'name',
        'name_e',
    ];
    protected $table = "boards_rent_sectors";
    // relations

    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Sector')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }

}
