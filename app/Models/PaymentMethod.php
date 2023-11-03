<?php

namespace App\Models;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\PointOfSale\Entities\Payment;
use Spatie\Activitylog\LogOptions;

class PaymentMethod extends Model
{
    use HasFactory, SoftDeletes, LogTrait;

    protected $table = 'general_payment_methods';

    protected $guarded = ['id'];

    public function scopeData($query)
    {
        return $query->select(
            'id',
            'name',
            'name_e',
            "is_default",
            'type',
            'status',
            'created_by',
        );
    }



    public function payments()
    {
        return $this->hasMany(Payment::class, 'payment_method_id');
    }

    public function documentHeader()
    {
        return $this->hasMany(DocumentHeader::class, 'payment_method_id');

    }


    public function hasChildren()
    {
        $relationsWithChildren = [];

        if ($this->payments()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'payments',
                'count' => $this->payments()->count(),
                'ids' => $this->payments()->pluck('id')->toArray()
            ];
        }

        if ($this->documentHeader()->count() > 0) {
                $relationsWithChildren[] = [
                    'relation' => 'documentHeader',
                    'count' => $this->documentHeader()->count(),
                    'ids' => $this->documentHeader()->pluck('id')->toArray()
                ];
        }


        return $relationsWithChildren;
    }

    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Payment Method')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }
}
