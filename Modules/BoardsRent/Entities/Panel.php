<?php

namespace Modules\BoardsRent\Entities;

use App\Traits\LogTrait;
use App\Traits\MediaTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\MediaLibrary\HasMedia;

class Panel extends Model implements HasMedia
{
    use HasFactory, LogTrait, MediaTrait;

    protected $fillable = [
        'id',
        'name',
        'name_e',
        'description',
        'description_e',
        'price',
        'code',
        'new_code',
        'size',
        'note',
        'face',
        'unit_status_id',
        'company_id',
        "category_id",
        "country_id",
        "governorate_id",
        "city_id",
        "avenue_id",
        "street_id",
        "lat",
        "lng",
        "is_double_face",
        "is_active",
        "is_multi",
        "price_year",
        "price_6month",
        "price_3month",
        "price_month",
        "price_2week",
    ];
    protected $table = "boards_rent_panels";


    public function scopeData($query)
    {
        return $query
            ->select('id','name','name_e','description','description_e','price','code','new_code','size','note','face','unit_status_id','category_id','country_id','governorate_id','city_id','avenue_id','street_id','lat','lng','price_year','price_6month','price_3month','price_month','price_2week','is_double_face','is_active','is_multi','company_id')
            ->with([
                'unitStatus:id,name,name_e','category:id,name,name_e','country:id,name,name_e','governorate:id,name,name_e',
                'city:id,name,name_e','avenue:id,name,name_e','street:id,name,name_e',
            ]);
    }

    protected $casts = [
        'price' => 'array',
    ];

    public function unitStatus()
    {
        return $this->belongsTo(UnitStatus::class, 'unit_status_id');
    }

    public function category()
    {

        return $this->belongsTo(\App\Models\Category::class, 'category_id');
    }

    public function country()
    {

        return $this->belongsTo(\App\Models\Country::class, 'country_id');
    }

    public function governorate()
    {

        return $this->belongsTo(\App\Models\Governorate::class, 'governorate_id');
    }

    public function city()
    {
        return $this->belongsTo(\App\Models\City::class, 'city_id');
    }

    public function avenue()
    {
        return $this->belongsTo(\App\Models\Avenue::class, 'avenue_id');
    }

    public function street()
    {
        return $this->belongsTo(\App\Models\Street::class, 'street_id');
    }

    public function orderDetails()
    {
        return $this->belongsToMany(OrderDetails::class, 'boards_rent_order_detail_panel', 'panel_id', 'order_detail_id');
    }

    public function packages()
    {
        return $this->belongsToMany(Package::class, 'boards_rent_package_panel', 'panel_id', 'package_id');
    }

    public function itemBreakDowns()
    {
        return $this->hasMany(\App\Models\ItemBreakDown::class, 'break_id')->where('module_type','panels');
    }

    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Panel')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }

    // attributes

    public function price(): Attribute
    {
        return Attribute::make(
            set:fn($value) => json_encode($value),
            get:fn($value) => json_decode($value, true),
        );

    }

    public function scopeFilter($query, $request)
    {
        return $query->where(function ($q) use ($request) {

            if ($request->has("packages")) {
                $q->WhereDoesntHave("packages");
            }


            if ($request->has('date')) {
                $q->whereDate('date', $request->date);
            }

            if ($request->country_id) {
                $q->where('country_id', $request->country_id);
            }

            if ($request->governorate_id && $request->governorate_id != 'null') {
                $q->where('governorate_id', $request->governorate_id);
            }

            if ($request->city_id && $request->city_id != 'null') {
                $q->where('city_id', $request->city_id);
            }

            if ($request->avenue_id && $request->avenue_id != 'null') {
                $q->where('avenue_id', $request->avenue_id);
            }

            if ($request->category_id && $request->category_id != 'null') {
                $q->where('category_id', $request->category_id);
            }

            if ($request->street_id && $request->street_id != 'null') {
                $q->where('street_id', $request->street_id);
            }

            if ($request->unit_status_id) {
                $q->where('unit_status_id', $request->unit_status_id);
            }
            if ($request->face && $request->face != 'null') {
                $q->where('face', $request->face);
            }

            if ($request->code && $request->code != 'null') {
                $q->where('code', $request->code);
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
}
