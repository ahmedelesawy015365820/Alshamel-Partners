<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Modules\HR\Entities\EndService;

class uniqueDateEndService implements Rule
{
    private $employeeId;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($employeeId)
    {
        $this->employeeId = $employeeId;
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
        $data = json_decode($value, true);

        $idYYYYMM = [];

        foreach ($data['data'] as $item) {
            $key = $item['id'] . '|' . $item['YYYYMM'];

            if (in_array($key, $idYYYYMM)) {
                return false; // Validation error: Duplicate combination of id and YYYYMM
            }

            $idYYYYMM[] = $key;
        }

        return true; // Validation successful
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute contains duplicate combinations of id and YYYYMM.';
    }
}
