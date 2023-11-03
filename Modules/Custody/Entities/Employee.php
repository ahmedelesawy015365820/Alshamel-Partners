<?php

namespace Modules\Custody\Entities;

use App\Models\Employee as GeneralEmployee;

class Employee extends GeneralEmployee
{

    // has many morpho with items
    public function items()
    {
        return $this->morphToMany(Item::class, 'cus_items', 'cus_items', 'model_type', 'model_id');
    }

}
