<?php

namespace Modules\ClubMembers\Entities;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;

class CmMemberRequest extends Model
{
    use HasFactory, LogTrait, SoftDeletes;

    protected $guarded = ['id'];
    protected $table = 'cm_member_requests';

    protected $casts = [
        'is_accept' => 'App\Enums\IsAccept',
        'is_sponsor' => 'App\Enums\IsSponsor',
        'membership_date' => 'date',
        'last_transaction_date' => 'date',
    ];

    public function cmTransaction()
    {
        return $this->hasOne(CmTransaction::class, 'member_request_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Member Request')
            ->setDescriptionForEvent(fn (string $eventName) => "This model has been {$eventName} by ($user)");
    }


    public function scopeFilter($query, $request)
    {
        return $query->where(function ($q) use ($request) {

            if ($request->has('date')) {
                $q->whereDate('date', $request->date);
            }

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

            if ($request->last_days) {
                $q->whereDate('membership_date', '>=', now()->subDays($request->last_days));
            }

            if ($request->has('cm_permission_id') && is_array($request->cm_permission_id)) {
                $q->whereHas('memberType', function ($q) use ($request) {
                    $q->whereHas('memberPermissions', function ($q) use ($request) {
                        foreach ($request->cm_permission_id as $permission_id) {
                            $q->whereJsonContains('cm_permissions_id', $permission_id);
                        }
                    });
                });
            }

            if ($request->key && $request->value) {
                $q->where($request->key, $request->value);
            }
        });
    }
}
