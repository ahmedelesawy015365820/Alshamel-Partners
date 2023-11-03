<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;


class GeneralMessageRequest extends Model
{
    use HasFactory, LogTrait;

    protected $table = 'general_message_requests';

    protected $guarded = ['id'];

    
    public function messageType()
    {
        return $this->belongsTo(MessageType::class, 'message_type_id');
    }
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'id');
    }

    public function getActivitylogOptions(): \Spatie\Activitylog\LogOptions
    {
        $user = @auth()->user()->id ?: "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('General Message Request')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }
}
