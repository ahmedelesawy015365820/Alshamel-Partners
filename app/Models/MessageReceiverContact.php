<?php

namespace App\Models;

use App\Models\GeneralMessageRequest;
use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageReceiverContact extends Model
{
    use HasFactory, LogTrait;

    protected $table = 'general_message_receiver_contacts';

    protected $guarded = ['id'];

    protected $observables = ['processedMessageRequests'];

    public function scopeData($query)
    {

        return $query
            ->select(
                'id',
                'message_request_id',
                'manager_employee_id',
            )->with('messageRequest:id,employee_id,message_date,request_mins', 'ManagerEmployee:id,name,name_e');
    }

    public function processMessageRequest()
    {

        $this->fireModelEvent('processedMessageRequests', false);

        GeneralMessageRequest::where('status', 'not processed')->update(['status'=> 'processed']);
    }

    public function messageRequest()
    {
        return $this->belongsTo(GeneralMessageRequest::class, 'message_request_id');
    }
    public function ManagerEmployee()
    {
        return $this->belongsTo(Employee::class, 'manager_employee_id');
    }

    public function getActivitylogOptions(): \Spatie\Activitylog\LogOptions
    {
        $user = @auth()->user()->id ?: "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Message Receiver Contact')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }
}
