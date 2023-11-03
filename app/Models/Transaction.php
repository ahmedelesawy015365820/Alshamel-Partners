<?php

namespace App\Models;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\RecievablePayable\Entities\RpBreakDown;

class Transaction extends Model
{
    use HasFactory, LogTrait, SoftDeletes;

    protected $table = "general_transactions";

    protected $guarded = ['id'];

    public function invoice()
    {
        return $this->belongsTo(\Modules\RealEstate\Entities\RlstInvoice::class, 'invoice_id');

    }

    public function breakDown()
    {
        return $this->belongsTo(RpBreakDown::class, 'break_id');

    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');

    }

    public function member()
    {
        return $this->belongsTo(\Modules\ClubMembers\Entities\CmMember::class, 'cm_member_id');

    }

    public function sponsor()
    {
        return $this->belongsTo(\Modules\ClubMembers\Entities\CmSponser::class, 'sponsor_id');

    }



    public function getActivitylogOptions(): \Spatie\Activitylog\LogOptions
    {
        $user = @auth()->user()->id ?: "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Transaction')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }

}
