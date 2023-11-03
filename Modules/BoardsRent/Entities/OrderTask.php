<?php

namespace Modules\BoardsRent\Entities;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;

class OrderTask extends Model
{
    use HasFactory, LogTrait;

    protected $table = 'boards_rent_order_tasks';
    protected $fillable = [
        'order_id', 'task_id', 'note', 'customer_id', 'user_id', 'department_id',
        'start_date', 'end_date', 'must_completed',
        'note','duration','company_id'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'must_completed' => \App\Enums\BooleanStatus::class,

    ];
    public function order()
    {
        return $this->belongsTo(\Modules\BoardsRent\Entities\Order::class, 'order_id');
    }

    public function task()
    {
        return $this->belongsTo(\Modules\BoardsRent\Entities\Task::class, 'task_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('OrderTask')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }

}
