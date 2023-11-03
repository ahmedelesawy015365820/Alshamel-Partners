<?php

namespace App\Models;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\RealEstate\Entities\RlstInvoice;
use Spatie\Activitylog\LogOptions;

class Serial extends Model
{
    use HasFactory, LogTrait;
    protected $table = "general_serials";

    protected $fillable = [
        'start_no',
        'perfix',
        'suffix',
        'restart_period_id',
        'company_id',
        'document_id',
        "branch_id",
        "name",
        "name_e",
        "gender",
    ];

    public function scopeData($query)
    {
        return $query->select(
            'id',
            'start_no',
            'perfix',
            'suffix',
            'restart_period_id',
            'document_id',
            "branch_id",
            "name",
            "name_e",
            "gender",
        )->with(['branch:id,name,name_e','document:id,name,name_e','restartPeriod:id,name,name_e']);
    }
    // protected $casts = [
    //     'is_default' => 'App\Enums\IsDefault',
    // ];

    // public function getIsDefault()
    // {
    //     return $this->is_default == 1 ? 'Default' : 'Non Default';
    // }

    protected $appends = [
        'has_child',
    ];

    public function orders()
    {
        return $this->hasMany(\Modules\BoardsRent\Entities\Order::class);
    }

    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function invoices()
    {
        return $this->hasMany(RlstInvoice::class, 'serial_id');
    }

    // public function hasChildren()
    // {
    //     return $this->orders()->count() > 0 ||
    //     $this->invoices()->count() > 0;
    // }

    public function hasChildren()
    {
        $relationsWithChildren = [];

        if ($this->orders()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'orders',
                'count' => $this->orders()->count(),
                'ids' => $this->orders()->pluck('id')->toArray()
            ];
        }
        if ($this->invoices()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'invoices',
                'count' => $this->invoices()->count(),
                'ids' => $this->invoices()->pluck('id')->toArray()
            ];
        }



        return $relationsWithChildren;
    }




    public function getHasChildAttribute()
    {
        if ($this->invoices()->count() > 0) {
            return 1;
        }
        return 0;
    }

    public function restartPeriod()
    {
        return $this->belongsTo(RestartPeriod::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Serial')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }
}
