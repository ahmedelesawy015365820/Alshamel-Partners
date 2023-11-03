<?php

namespace Modules\BoardsRent\Entities;

use App\Models\Category;
use App\Models\Governorate;
use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;

class Package extends Model
{
    use HasFactory, LogTrait;

    protected $fillable = [
        'name',
        'name_e',
        'code',
        'price',
        'company_id',
        'note',
        'category_id',
        'governorate_id',
    ];
    protected $table = "boards_rent_packages";

    public function scopeData($query)
    {
        return $query->select('id','name','name_e','code','price','company_id','note','category_id','governorate_id')
            ->with([
                'category:id,name,name_e',
                'governorate:id,name,name_e',
            ]);
    }

    // relations

    public function panels()
    {
        return $this->belongsToMany(\Modules\BoardsRent\Entities\Panel::class, 'boards_rent_package_panel', 'package_id', 'panel_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class,'category_id');
    }

    public function governorate()
    {
        return $this->belongsTo(Governorate::class,'governorate_id');
    }

    public function hasChildren()
    {
        return $this->panels()->count() > 0;
    }

    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Package')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
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
