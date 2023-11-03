<?php

namespace App\Models;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\BoardsRent\Entities\Panel;
use Spatie\Activitylog\LogOptions;

class ItemBreakDown extends Model
{
    use HasFactory, LogTrait, SoftDeletes;

    protected $table = 'general_item_break_downs';

    protected $guarded = ["id"];

    public function panel()
    {
        return $this->belongsTo(Panel::class,'break_id');
    }
    public function documentHeaderDetail()
    {
        return $this->belongsTo(DocumentHeaderDetail::class,'document_header_detail_id');

    }


    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return LogOptions::defaults()
            ->logAll()
            ->useLogName('ItemBreakDown')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }



}
