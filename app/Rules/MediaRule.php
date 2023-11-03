<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(private $type = null)
    {
        $this->type = $type;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {

        if ($this->type == null) {
            $media = Media::find($value);
            if ($media) {
                if ($media->model_type == 'App\Models\File') {
                    return true;
                }
            } else {
                return true;
            }

            return false;
        } else {
            $media = Media::where(function ($q) use ($value) {
                $q->where('model_type', $this->type);
                if ($this->type != 'App\Models\Setting') {
                    $q->where('model_id', request()->id);
                }

                $q->where('id', $value);
            })->first();

            if ($media) {
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        if (!$this->type) {
            return 'the is media already assigned to another model.';
        } else {
            return 'this media is not belong to this model.';
        }
    }
}
