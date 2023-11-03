<?php

namespace App\Models;

use App\Traits\MediaTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;

class File extends Model implements HasMedia
{
    protected $table = "general_files";
    use HasFactory, MediaTrait;

}
