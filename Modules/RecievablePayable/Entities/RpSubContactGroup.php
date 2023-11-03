<?php

namespace Modules\RecievablePayable\Entities;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\GL\Entities\GlCoa;

class RpSubContactGroup extends Model
{
    use HasFactory,LogTrait;

    protected $guarded = ['id'];

    protected static function newFactory()
    {
        return \Modules\RecievablePayable\Database\factories\RpSubContactGroupFactory::new();
    }
    public function mainContactGroup(){
        return $this->belongsTo(RpMainContactGroup::class,"rp_main_contact_group_id");
    }
    public function glAccount(){
        return $this->belongsTo(GlCoa::class,"gl_acc_no");
    }
}
