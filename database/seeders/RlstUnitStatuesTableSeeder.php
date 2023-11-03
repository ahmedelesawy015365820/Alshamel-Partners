<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\RealEstate\Entities\RlstUnitStatus;

class RlstUnitStatuesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('rlst_unit_statuses')->delete();

        $attributes = [
            [
                'id' => 1,
                'name' => 'متاح',
                'name_e' => 'free',
            ],
            [
                'id' => 2,
                'name' => 'محجوز',
                'name_e' => 'reserved',
            ],
            [
                'id' => 3,
                'name' => 'مباع',
                'name_e' => 'Sold',
            ],
            [
                'id' => 4,
                'name' => 'تحت الصيانه',
                'name_e' => 'under_maintinance',
            ],
        ];

        foreach ($attributes as $attribute) {
            RlstUnitStatus::create($attribute);
        }
    }
}
