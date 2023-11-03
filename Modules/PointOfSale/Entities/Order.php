<?php

namespace Modules\PointOfSale\Entities;

use App\Models\Branch;
use App\Models\GeneralCustomer;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected $table = "pos_orders";

    protected $appends = ['sub_discount'];
    protected $casts = [
        'sub_total'   => 'float',
        'total_tax'   => 'integer' ,
        'due_amount'  => 'float',
        'total'       => 'float',
        'all_discount'=> 'float',
        'customer_id' => 'integer',
        'supplier_id' => 'integer',
        'branch_id'   => 'integer',

    ];
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
    public function customer()
    {
        return $this->belongsTo(GeneralCustomer::class, 'customer_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
    public function getSubDiscountAttribute($key)
    {
        return $this->items->where('type', 'discount')->first()->price ?? 0;
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

            if ($request->customer_id) {
                $q->where('customer_id', $request->customer_id);
            }

            if ($request->supplier_id) {
                $q->where('supplier_id', $request->supplier_id);
            }

            if ($request->payment_method_id) {
                $q->whereHas('payments', function ($q) use ($request) {
                    $q->where('payment_method_id', $request->payment_method_id);
                });
            }

            if ($request->start_date && $request->end_date) {
                $q->whereBetween('date', [$request->start_date, $request->end_date]);
            };

            if ($request->category_id) {
                $q->whereHas('items', function ($q) use ($request) {
                    $q->whereHas('product', function ($q) use ($request) {
                        $q->where('category_id', $request->category_id);
                    });
                });
            }
            if ($request->brand_id) {
                $q->whereHas('items', function ($q) use ($request) {
                    $q->whereHas('product', function ($q) use ($request) {
                        $q->where('brand_id', $request->brand_id);
                    });
                });
            }
            if ($request->group_id) {
                $q->whereHas('items', function ($q) use ($request) {
                    $q->whereHas('product', function ($q) use ($request) {
                        $q->where('group_id', $request->group_id);
                    });
                });
            }

            if ($request->created_by) {
                $q->where('created_by', $request->created_by);
            }

            // Filter for today
            if ($request->has('today')) {
                $today = Carbon::today()->toDateString();
                $q->whereDate('date', $today);
            }

            // Filter for last 7 days
            if ($request->has('last_7_days')) {
                $sevenDaysAgo = Carbon::today()->subDays(7)->toDateString();
                $q->whereDate('date', '>=', $sevenDaysAgo);
            }

            // Filter for last 30 days
            if ($request->has('last_30_days')) {
                $thirtyDaysAgo = Carbon::today()->subDays(30)->toDateString();
                $q->whereDate('date', '>=', $thirtyDaysAgo);
            }

            // Filter for last month
            if ($request->has('last_month')) {
                $firstDayOfLastMonth = Carbon::today()->subMonth()->firstOfMonth()->toDateString();
                $lastDayOfLastMonth = Carbon::today()->subMonth()->lastOfMonth()->toDateString();
                $q->whereBetween('date', [$firstDayOfLastMonth, $lastDayOfLastMonth]);
            }

        });
    }

    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Order')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }




}
