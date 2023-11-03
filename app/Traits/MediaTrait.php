<?php

namespace App\Traits;

use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\InteractsWithMedia;

trait MediaTrait
{
    use InteractsWithMedia;

    public function getFilesAttribute()
    {
        $media = $this->getMedia('media');
        if (!$media->isEmpty()) {
            return $media;
        } else {
            return null;
        }
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaConversion('media')
            ->format(Manipulations::FORMAT_WEBP)
            ->nonQueued();
    }
}
