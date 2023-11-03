<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SalesmenTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'name' => "بائع",
                'name_e' => "salesman",
            ],
            [
                'name' => 'قائد فريق',
                'name_e' => 'group leader',

            ],
            [
                'name' => 'مدير مبيعات',
                'name_e' => 'sales manager',
            ],
            [
                'name' => 'مدير قسم',
                'name_e' => 'department lead',
            ],
            [
                'name' => 'مدير عام',
                'name_e' => 'general manger'
            ],
        ];




        foreach ($data as $key => $value) {
            \App\Models\SalesmenType::create($value);
        }
    }
}
