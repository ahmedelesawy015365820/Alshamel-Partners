<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class NotInChildrenRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {

        $module = \App\Models\Module::where(["id" => $value , "parent_id" => request()->id])->first();
        if ($module) {
            return false;
        }
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('message.parent can not be child');
    }
}
