<?php

namespace Modules\ClubMembers\Entities;

use App\Models\Branch;
use App\Models\Document;
use App\Models\Serial;
use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;

class CmTransaction extends Model
{
    use HasFactory, LogTrait, SoftDeletes;

    protected $guarded = ['id'];
    protected $table = 'cm_transactions';
    /*
    protected $casts = [

        'date' => 'date',
    ];
    */

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');

    }
    public function document()
    {
        return $this->belongsTo(Document::class, 'document_id');

    }
    public function serial()
    {
        return $this->belongsTo(Serial::class, 'serial_id');

    }
    public function member()
    {
        return $this->belongsTo(\Modules\ClubMembers\Entities\CmMember::class, 'cm_member_id');

    }

    public function sponsor()
    {
        return $this->belongsTo(\Modules\ClubMembers\Entities\CmSponser::class, 'sponsor_id');
    }

    public function memberRequest()
    {
        return $this->belongsTo(CmMemberRequest::class, 'member_request_id');
    }

    public function scopeFilter($query, $request)
    {
        return $query->where(function ($q) use ($request) {

            if ($request->search && $request->columns) {
                foreach ($request->columns as $column) {
                    if (strpos($column, ".") > 0) {
                        $column = explode(".", $column);
                        $q->orWhereRelation($column[0], $column[1], 'like', '%' . $request->search . '%');
                        $q->orWhereHas($column[0], function ($q) use ($column, $request) {
                            $q->where($column[1], 'like', '%' . $request->search . '%');
                        });
                    } else {
                        $q->orWhere($column, 'like', '%' . $request->search . '%');
                    }
                }
            }

            if ($request->key && $request->value) {
                $q->where($request->key, $request->value);
            }
        });
    }

    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('CmTransaction')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }

}
