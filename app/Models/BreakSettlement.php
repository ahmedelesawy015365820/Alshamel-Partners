<?php

namespace App\Models;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\RecievablePayable\Entities\RpBreakDown;

class BreakSettlement extends Model
{
    use HasFactory, LogTrait ,SoftDeletes;
    protected $table = 'general_break_settlements';

    protected $guarded = ['id'];

    public function document()
    {
        return $this->belongsTo(Document::class,'document_id','id');

    }
    public function documentNumber()
    {
        return $this->belongsTo(Document::class,'document_number','id');

    }
    public function rpBreakDown()
    {
        return $this->belongsTo(RpBreakDown::class,'break_id','id');

    }

    public function rpBreakDownDocument()
    {
        return $this->belongsTo(RpBreakDown::class,'break_break_id','break_id');

    }

    public function scopeData($query)
    {
        return $query->select('document_id', 'document_number', 'amount','break_id','break_break_id');
    }

    public function breakVoucherHeaders()
    {
        return $this->hasMany(VoucherHeader::class,'break_settlement_id','id');

    }





    public function getActivitylogOptions(): \Spatie\Activitylog\LogOptions
    {
        $user = @auth()->user()->id ?: "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Break Settlement')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }
}
