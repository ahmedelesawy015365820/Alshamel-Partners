<?php

namespace Modules\BoardsRent\Entities;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;

class Task extends Model
{
    // use HasTranslations;
    use LogTrait;
    protected $table = 'boards_rent_tasks';
    protected $fillable = [
        'department_id', 'name', 'name_e', 'description', 'description_e',
    ];

    public function department()
    {
        return $this->belongsTo(\App\Models\Depertment::class, 'department_id');
    }
    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Task')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }

}
