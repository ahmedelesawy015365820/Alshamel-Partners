<?php

namespace Modules\ClubMembers\Entities;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\ClubMembers\Entities\Status;
use Spatie\Activitylog\LogOptions;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;


class CmMember extends Model
{
    use HasFactory, LogTrait, SoftDeletes;

    protected $guarded = [];
    protected $table = 'cm_members';

    protected $casts = [
        'is_accept' => 'App\Enums\IsAccept',
        'is_sponsor' => 'App\Enums\IsSponsor',
        'membership_date' => 'date',
        'last_transaction_date' => 'date',
    ];

    public function getLastNameAttribute($value)
    {
        if ($value)
        {
            return $value;
        }else{
            return '';
        }
    }


    public function status()
    {
        return $this->belongsTo(CmMemberStatus::class, 'member_status_id');
    }


    public function sponsors()
    {
        return $this->belongsTo(\Modules\ClubMembers\Entities\CmSponser::class, 'sponsor_id');
    }

    public function memberType()
    {
        return $this->belongsTo(CmMemberType::class, 'member_kind_id');
    }

    public function cmTransaction()
    {
        return $this->hasMany(CmTransaction::class, 'cm_member_id');

    }
    public function lastCmTransaction()
    {
        return $this->hasOne(CmTransaction::class, 'cm_member_id')->latestOfMany();

    }

    public function financialStatus()
    {
        return $this->belongsTo(CmFinancialStatus::class, 'financial_status_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Sponser')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }



    // public function scopeTransaction($query)
    // {
    //     return $query->with(['cmTransaction' => function ($query) {
    //         return $query->orderBy('year_to', 'desc')->get();
    //     }]);

    // }

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
                        if ($column == 'membership_number')
                        {
                            $q->orWhere($column, $request->search);
                        }elseif($column == 'full_name'){
                            $q->orWhere($column, 'like', $request->search . '%');
                        }elseif($column == 'national_id'){
                            $q->orWhere($column, $request->search);
                        }else{
                            $q->orWhere($column, 'like', '%' . $request->search . '%');
                        }
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


    public function scopeWithValidLastTransactionDate(Builder $query)
    {  
        return $query->whereHas('cmTransaction', function($q){
            $q->where("year", DB::table('general_financial_years')->where('is_active', 1)->pluck('year'));
        })->get();
    }
    
    
    public function lastTransactionDate()
    {
        return $this->whereHas('cmTransaction', function($q){
            $q->where('year', 2024);
        })->count();
        
        //->where('year', DB::table('general_financial_years')
        //->where('is_active', 1)->year)->min('date')->pluck('date', 'year')->get();
        // DB::table('general_financial_years')->where('is_active', 1)->year
    }

    /*
    public function memberLastTransactionYear()
    {

        

    }
    */
    /*
    foreach($employees_id as $employee_id)
    {
        $employees_timetables_header_id= EmployeesTimetablesDetails::where('employee_id', $employee_id);

        $timetables_header_id = EmployeesTimetablesHeader::where('id', employees_timetables_header_id);

        $id = TimetablesHeader::where('id', $timetables_header_id );
        
        $details = TimetablesDetail::where('id', $id);

    }
    */
    
}
