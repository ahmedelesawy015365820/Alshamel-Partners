<?php

namespace Modules\RealEstate\Entities;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Relations\Pivot;

class RlstInvoiceItem extends Pivot
{
    use HasFactory, LogTrait;

    protected $table = 'rlst_invoices_item';

    protected $fillable = [
        'invoice_id',
        'company_id',
        'item_id',
        'quantity',
        'amount',
    ];

    public function invoice()
    {
        return $this->belongsTo(RlstInvoice::class, 'invoice_id');
    }

    public function item()
    {
        return $this->belongsTo(RlstItem::class, 'item_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Real Estate  Building Wallet')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }
}
