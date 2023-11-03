<?php

namespace Modules\RecievablePayable\Entities;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RpInstallmentStatus extends Model
{
    use HasFactory,LogTrait;

    protected $guarded = ['id'];


    protected static function newFactory()
    {
        return \Modules\RecievablePayable\Database\factories\RpInstallmentStatusFactory::new();
    }
}
