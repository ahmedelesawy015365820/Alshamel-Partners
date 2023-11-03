<?php

namespace Modules\RealEstate\Entities;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;

class RlstCategoryItem extends Model
{
    use HasFactory, LogTrait, SoftDeletes;

    protected $fillable = [
        'name',
        'name_e',
        'parent_id',
        'company_id',

    ];

    protected $appends = ['haveChildren'];

    protected $table = 'rlst_category_item';

    public function children()
    {
        return $this->hasMany(RlstCategoryItem::class, 'parent_id', 'id');
    }

    public function parent()
    {
        return $this->belongsTo(RlstCategoryItem::class, 'parent_id', 'id');
    }

    public function getHaveChildrenAttribute()
    {
        return static::where("parent_id", $this->id)->count() > 0;
    }

    // relations

    public function items()
    {
        return $this->belongsToMany(\Modules\RealEstate\Entities\RlstItem::class, 'rlst_categories_item', 'category_item_id', 'item_id');

    }


    public function hasChildren()
    {
        $relationsWithChildren = [];

        if ($this->items()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'items',
                'count' => $this->items()->count(),
                'ids' => $this->items()->pluck('id')->toArray()
            ];
        }
        if ($this->children()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'Category',
                'count' => $this->children()->count(),
                'ids' => $this->children()->pluck('id')->toArray()
            ];
        }

        return $relationsWithChildren;
    }

    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Property Type')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }
}
