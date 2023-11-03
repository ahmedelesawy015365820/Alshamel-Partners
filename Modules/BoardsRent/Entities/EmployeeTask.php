<?php

namespace Modules\BoardsRent\Entities;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\LogOptions;

class EmployeeTask extends Model
{
    use HasFactory, LogTrait;

    protected $table = "boards_rent_employee_tasks";

    protected $guarded = ['id'];

    protected static function newFactory()
    {
        return \Modules\BoardsRent\Database\factories\EmployeeTaskFactory::new();
    }

    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('EmployeeTask')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }
}
