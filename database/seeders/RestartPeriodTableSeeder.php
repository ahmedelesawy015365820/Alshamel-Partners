<?php

namespace Database\Seeders;

use App\Models\RestartPeriod;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RestartPeriodTableSeeder extends Seeder
{
    /**
     * Run the databse seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('general_restart_period')->delete();

        $attributes = [
//            [
//                'id' => 1,
//                'name' => 'يومي',
//                'name_e' => 'daily',
//            ],
//            [
//                'id' => 2,
//                'name' => 'اسبوعي',
//                'name_e' => 'weekly',
//            ],
//            [
//                'id' => 3,
//                'name' => 'شهري',
//                'name_e' => 'monthly',
//            ],
//            [
//                'id' => 4,
//                'name' => 'ربع سنوي',
//                'name_e' => 'quarterly',
//            ],
            [
                'id' => 5,
                'name' => 'سنوي',
                'name_e' => 'yearly',
            ],
            [
                'id' => 6,
                'name' => 'مستمر',
                'name_e' => 'continuous',
            ],
//            [
//                'id' => 7,
//                'name' => 'نصف سنوي',
//                'name_e' => 'midterm',
//            ],

        ];

        foreach ($attributes as $attribute) {
            RestartPeriod::create($attribute);
        }
    }
}
