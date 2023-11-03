<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\HR\Entities\TimeTableType;

class HrTimeTableTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('hr_timetable_types')->delete();

        $attributes = [
           [
               'id' => 1,
               'name' => 'دوام ثابت شيفت واحد',
               'name_e' => 'Fixed time, one shift',
           ],
           [
               'id' => 2,
               'name' => 'دوام ثابت شيفتين',
               'name_e' => 'Fixed time, two shifts',
           ],
           [
               'id' => 3,
               'name' => 'حر',
               'name_e' => 'Free',
           ]


        ];

        foreach ($attributes as $attribute) {
            TimeTableType::create($attribute);
        }
    }
}
