<?php

namespace Modules\HR\Entities;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TimeTableType extends Model
{
    use HasFactory,LogTrait;

    protected $fillable = [
        'name',
        'name_e',
    ];
    protected $table = 'hr_timetable_types';

    public function scopeData($query)
    {
        return $query->select('id', 'name', 'name_e');
    }
}
