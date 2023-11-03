<?php

namespace App\Rules;

use App\Models\Company;
use Illuminate\Contracts\Validation\Rule;

class BranchUniqueCompanyRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(private $company_id, private $branch_id = null)
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
       // unique in company
         $company = Company::find($this->company_id);
            $branch = $company->branches()->where('name', $value)->first();
            if ($branch) {
                if ($this->branch_id) {
                    if ($branch->id == $this->branch_id) {
                        return true;
                    }
                }
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
        return __("message.field already exists");
    }
}
