<?php

namespace App\Models;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DatabaseBackup extends Model
{
    use HasFactory, SoftDeletes, LogTrait;
    protected $table = 'general_database_backup';

    protected $fillable = ['path'];

    public function getFullPathAttribute()
    {
        return asset('uploads/' . env('APP_NAME') . '/' . $this->path);
    }
}
