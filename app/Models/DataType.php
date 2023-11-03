<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataType extends Model
{
    use HasFactory;
    protected $table = 'general_data_types';

    protected $guarded = ['id'];

    public function scopeData($query)
    {
        return $query
            ->select('id', 'name', 'name_e', 'placeholder_e', 'placeholder', 'length');
    }

    public function archiveFiles()
    {
        return $this->hasMany(\Modules\Archiving\Entities\ArchiveFile::class, 'data_type_id');
    }

    public function documentFields()
    {
        return $this->hasMany(\Modules\Archiving\Entities\DocumentField::class, 'data_type_id');
    }
}
