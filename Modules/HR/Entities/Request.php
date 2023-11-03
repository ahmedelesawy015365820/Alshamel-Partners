<?php

namespace Modules\HR\Entities;

use App\Models\Employee;
use App\Traits\LogTrait;
use App\Traits\MediaTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\HasMedia;


class Request extends Model implements HasMedia
{
    use HasFactory, MediaTrait;

    protected $guarded = [''];

    protected $table = 'hr_requests';

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function approved()
    {
        return $this->belongsTo(Employee::class, 'approved_by');
    }

    public function requestType()
    {
        return $this->belongsTo(RequestType::class, 'request_types_id');
    }

    public function requestStatus()
    {
        return $this->belongsTo(Status::class, 'request_status_id');
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
                        // $q->orWhereHas($column[0], function ($q) use ($column, $request) {
                        //     $q->where($column[1], 'like', '%' . $request->search . '%');
                        // });
                    } else {
                        $q->orWhere($column, 'like', '%' . $request->search . '%');
                    }
                }
            }

            if ($request->parent_id) {
                $q->where('parent_id', $request->parent_id);
            }
            if (request()->header('company-id')) {
                if (in_array('company_id', $this->fillable)) {
                    $q->where('company_id', request()->header('company-id'));
                }
            }

            if ($request->key && $request->value) {
                $q->where($request->key, $request->value);
            }
        });
    }

    protected function asJson($value)
    {
        return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    // protected function dataTypeValue(): Attribute
    // {
    //     return Attribute::make(
    //         set:fn($value) => json_encode($value, JSON_UNESCAPED_UNICODE),
    //         get:fn($value) => json_decode($value)
    //     );
    // }

//
    // public function getActivitylogOptions(): LogOptions
    // {
    //     $user = auth()->user()->id ?? "system";

    //     return \Spatie\Activitylog\LogOptions::defaults()
    //         ->logAll()
    //         ->useLogName('Request')
    //         ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    // }

}
