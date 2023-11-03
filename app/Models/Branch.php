<?php

namespace App\Models;

use App\Traits\LogTrait;
use App\Traits\MediaTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\PointOfSale\Entities\Order;
use Modules\PointOfSale\Entities\Product;
use Spatie\Activitylog\LogOptions;
use Spatie\MediaLibrary\HasMedia;

class Branch extends Model implements HasMedia
{
    use HasFactory, LogTrait, MediaTrait;
    protected $table = 'general_branches';

    protected $guarded = [];

    public function scopeData($query)
    {
        return $query
            ->select('id', 'name', 'name_e', 'is_active', 'parent_id', 'code_country', 'phone', 'second_phone', 'fax', 'p_o_pox', 'address', 'email')
            ->with([
                'parent:id,name,name_e',
                'children',
                'serials',
                'stores',
                'media',
            ]);
    }

    public function stores()
    {
        return $this->hasMany(Store::class, 'branch_id');
    }

    public function serials()
    {
        return $this->hasMany(Serial::class, 'branch_id');
    }

    public function children()
    {
        return $this->hasMany(Branch::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(Branch::class, 'parent_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function hasChildren()
    {
        $relationsWithChildren = [];

        if ($this->stores()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'stores',
                'count' => $this->stores()->count(),
                'ids' => $this->stores()->pluck('id')->toArray(),
            ];
        }
        if ($this->serials()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'serials',
                'count' => $this->serials()->count(),
                'ids' => $this->serials()->pluck('id')->toArray(),
            ];
        }

        if ($this->children()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'branches',
                'count' => $this->children()->count(),
                'ids' => $this->children()->pluck('id')->toArray(),
            ];
        }
        if ($this->products()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'products',
                'count' => $this->products()->count(),
                'ids' => $this->products()->pluck('id')->toArray(),
            ];
        }
        if ($this->orders()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'orders',
                'count' => $this->orders()->count(),
                'ids' => $this->orders()->pluck('id')->toArray(),
            ];
        }

        return $relationsWithChildren;
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Branch')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }

}
