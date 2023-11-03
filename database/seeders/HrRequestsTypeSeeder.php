<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\HR\Entities\RequestType;

class HrRequestsTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('hr_requests_types')->delete();

        $attributes = [
            [
                'id' => 1,
                'name' => 'طلب اجازه مرضى',
                'name_e' => 'Sick leave request',
            ],
            [
                'id' => 2,
                'name' => 'طلب اجازه عاديه',
                'name_e' => 'Ordinary leave request',
            ],
            [
                'id' => 3,
                'name' => 'طلب اجازه طارئه',
                'name_e' => 'Emergency leave request',
            ],
            [
                'id' => 4,
                'name' => 'طلب سلفه',
                'name_e' => 'predecessor request',
            ],
            [
                'id' => 5,
                'name' => 'طلب استئذان',
                'name_e' => 'permission request',
            ],
            [
                'id' => 6,
                'name' => 'طلب شهادة راتب',
                'name_e' => 'Payroll request',
            ],
            [
                'id' => 7,
                'name' => 'طلب استقاله',
                'name_e' => 'Resignation request',
            ],

        ];

        foreach ($attributes as $attribute) {
            RequestType::create($attribute);
        }
    }
}
